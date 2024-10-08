<?php
use CRM_Civirfm_ExtensionUtil as E;

/**
 * ContactRfm.Refreshexpired API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_contact_rfm_Refreshexpired_spec(&$spec) {
}

/**
 * ContactRfm.Refreshexpired API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_contact_rfm_Refreshexpired($params) {
  $count_expired_records = CRM_Civirfm_Utils::refreshExpired();
  $returnValues[] = [
    'count' => $count_expired_records
  ];
  return civicrm_api3_create_success($returnValues, $params, 'ContactRfm', 'Refreshexpired');
}
