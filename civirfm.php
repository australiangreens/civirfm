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

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function civirfm_civicrm_preProcess($formName, &$form): void {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function civirfm_civicrm_navigationMenu(&$menu): void {
//  _civirfm_civix_insert_navigation_menu($menu, 'Mailings', [
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ]);
//  _civirfm_civix_navigationMenu($menu);
//}

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