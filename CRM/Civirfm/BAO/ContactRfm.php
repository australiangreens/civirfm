<?php
// phpcs:disable
use CRM_Civirfm_ExtensionUtil as E;
// phpcs:enable

class CRM_Civirfm_BAO_ContactRfm extends CRM_Civirfm_DAO_ContactRfm {

  /**
   * Create a new ContactRfm based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Civirfm_DAO_ContactRfm|NULL
   */
  /*
  public static function create($params) {
    $className = 'CRM_Civirfm_DAO_ContactRfm';
    $entityName = 'ContactRfm';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }
  */

}
