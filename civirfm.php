<?php

require_once 'civirfm.civix.php';
// phpcs:disable
use CRM_Civirfm_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function civirfm_civicrm_config(&$config): void {
  _civirfm_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function civirfm_civicrm_install(): void {
  _civirfm_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function civirfm_civicrm_enable(): void {
  _civirfm_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function civirfm_civicrm_navigationMenu(&$menu): void {
 _civirfm_civix_insert_navigation_menu($menu, 'Administer/CiviContribute', [
   'label' => E::ts('CiviRFM Settings'),
   'name' => 'civirfm_settings',
   'url' => 'civicrm/admin/setting/civirfm',
   'permission' => 'access civirfm',
   'operator' => 'OR',
   'separator' => 0,
 ]);
 _civirfm_civix_navigationMenu($menu);
}

/**
 * Implements hook_civicrm_permission().
 */
function civirfm_civicrm_permission(&$permissions) {
  $prefix = E::ts('CiviCRM RFM extension: ');
  $permissions['administer civirfm'] = [
    $prefix . E::ts('Administer CiviRFM'),
    E::ts('Manage RFM settings')
  ];
  $permissions['access civirfm'] = [
    $prefix . E::ts('Access CiviRFM'),
    E::ts('Access CiviRFM')
  ];
}

/**
 * Implements hook_civicrm_tabset().
 * 
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_tabset
 */
function civirfm_civicrm_tabset($tabsetName, &$tabs, $context) {
  if ($tabsetName === 'civicrm/contact/view' && CRM_Core_Permission::check('access civirfm')) {
    // add a tab to the contact summary screen
    $contactId = $context['contact_id'];
    $url = CRM_Utils_System::url('civicrm/contact/view/rfm', ['cid' => $contactId]);

    $tabs[] = [
      'id' => 'rfm_contact',
      'url' => $url,
      'title' => E::ts('RFM'),
      'weight' => 1,
      'icon' => 'crm-i fa-usd',
    ];
  }
}

/**
 * Implements hook_civicrm_post().
 * 
 * Every time a Contribution is created or edited and has a status of "Completed"
 * we queue up a job to calculate the RFM values for the associated contact.
 * 
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_post
 */
function civirfm_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  if ($objectName != 'Contribution' || ($op != 'create' && $op != 'edit')) {
    return;
  }
  // Test for Completed contrib status, exit if not
  if ($objectRef->contribution_status_id != CRM_Core_PseudoConstant::getKey('CRM_Contribute_BAO_Contribution', 'contribution_status_id', 'Completed')) {
    return;
  }
  // Test for relevant financial type(s) if applicable, exit if not
  $fin_types = (Civi::settings()->get('civirfm_fin_types')) ? explode(',', Civi::settings()->get('civirfm_fin_types')) : NULL;
  if (!is_null($fin_types) && !in_array($objectRef->financial_type_id, $fin_types)) {
    return;
  }
  // Grab contact ID and queue up a job
  $objectRef->find(TRUE);
  $params = [
    'contact_id' => $objectRef->contact_id,
  ];
  $queue = CRM_Civirfm_Queue::singleton()->getQueue();
  $task = new CRM_Queue_Task(
    ['CRM_Civirfm_Utils', 'processRFMTask'],
    [$params]
  );
  $queue->createItem($task);
  return;
}

/**
 * Implements hook_civicrm_merge().
 * 
 * Whenever contacts are merged, check to see if either contact has RFM values.
 * If so, queue up a job to calculate the RFM values for the surviving contact.
 * 
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_merge
 */
function civirfm_civicrm_merge($type, &$data, $mainId = NULL, $otherId = NULL, $tables = NULL) {
  $result = \Civi\Api4\ContactRfm::get(FALSE)
    ->addSelect('id')
    ->addClause('OR', ['contact_id', '=', $mainId], ['contact_id', '=', $otherId])
    ->execute();
  if ($result->rowCount) {
    // Delete existing records
    foreach ($result as $rfm_record) {
      \Civi\Api4\ContactRfm::delete(FALSE)
        ->addWhere('id', '=', $rfm_record['id'])
        ->execute();
    }
    $params = [
      'contact_id' => $mainId,
    ];
    $queue = CRM_Civirfm_Queue::singleton()->getQueue();
    $task = new CRM_Queue_Task(
      ['CRM_Civirfm_Utils', 'processRFMTask'],
      [$params]
    );
    $queue->createItem($task);
  }
  return;
}