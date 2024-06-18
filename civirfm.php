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
  Civi::service('dispatcher')->addListener('hook_civicrm_navigationMenu', 'civirfm_symfony_navigationMenu', 100);
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
function civirfm_symfony_navigationMenu($event): void {
  $hook_values = $event->getHookValues();
  $menu = &$hook_values[0];
  _civirfm_civix_insert_navigation_menu($menu, 'Administer/CiviModels', [
    'label' => E::ts('Donor RFM Model Settings'),
    'name' => 'civirfm_settings',
    'url' => 'civicrm/admin/setting/civirfm',
    'permission' => 'administer civirfm',
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
    'label' => $prefix . E::ts('Administer CiviRFM'),
    'description' => E::ts('Manage RFM settings')
  ];
  $permissions['access civirfm'] = [
    'label' => $prefix . E::ts('Access CiviRFM'),
    'description' => E::ts('View CiviRFM data')
  ];
}

/**
 * Implements hook_civicrm_post().
 *
 * Every time a Contribution is created or edited and has a status of "Completed"
 * we queue up a job to calculate the RFM values for the associated contact.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_post
 */
function civirfm_civicrm_postCommit($op, $objectName, $objectId, &$objectRef) {
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
  civirfm_post_contribution_callback($objectId);
  return;
}

function civirfm_post_contribution_callback($objectId) {
  // Grab contact ID and queue up a job
  $dao = new CRM_Contribute_DAO_Contribution();
  $dao->id = $objectId;
  $dao->find(TRUE);
  $params = [
    'contact_id' => $dao->contact_id,
  ];
  civirfm_create_queue_task($params);
}

function civirfm_create_queue_task($params) {
  $queue = CRM_Civirfm_Queue::singleton()->getQueue();
  $task = new CRM_Queue_Task(
    ['CRM_Civirfm_Utils', 'processRFMTask'],
    [$params],
    'Calculate RFM values'
  );
  $queue->createItem($task);
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
    civirfm_create_queue_task($params);
  }
  return;
}

/**
 * Implements hook_displayCiviModelData().
 *
 * Builds data payload for CiviModels extension display
 *
 * @link https://github.com/australiangreens/
 */
function civirfm_civimodels_displayCiviModelData($contact_id, &$data) {
  if (!CRM_Core_Permission::check('access civirfm')) {
    return;
  }
  $contactRfm = \Civi\Api4\ContactRfm::get(FALSE)
  ->addSelect('id', 'contact_id', 'recency', 'frequency', 'monetary', 'date_calculated')
  ->addWhere('contact_id', '=', $contact_id)
  ->execute()
  ->first(); // we can safely assume there is only a single ContactRfm record per contact

  if (isset($contactRfm['date_calculated'])) {
    $civirfm = [
      'contact_id' => $contact_id,
      'recency' => $contactRfm['recency'],
      'frequency' => $contactRfm['frequency'],
      'monetary' => $contactRfm['monetary'],
      'date_calculated' => $contactRfm['date_calculated'],
      'rfm_time' => \Civi::settings()->get('civirfm_rfm_period'),
      'curr_symbol' => CRM_Core_Config::singleton()->defaultCurrencySymbol,
      'template' => 'CRM/Civirfm/Page/ContactRfm.tpl'
    ];
    $data['civirfm'] = $civirfm;
  } else {
    return;
  }
}
