<?php
use CRM_Civirfm_ExtensionUtil as E;

/**
 * ContactRfm.Runqueue API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_contact_rfm_Runqueue_spec(&$spec) {
}

/**
 * ContactRfm.Runqueue API
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
function civicrm_api3_contact_rfm_Runqueue($params) {
  $returnValues = [];
  // retrieve the queue
  $queue = CRM_Civirfm_Queue::singleton()->getQueue();
  $runner = new CRM_Queue_Runner([
    'title' => E::ts('CiviRFM Queue Runner'),
    'queue' => $queue,
    'errorMode' => CRM_Queue_Runner::ERROR_CONTINUE,
  ]);

  // stop executing next item after 5 minutes
  $maxRunTime = time() + 600;
  $continue = TRUE;
  while (time() < $maxRunTime && $continue) {
    $result = $runner->runNext();
    if (!$result['is_continue']) {
      // all items in the queue are processed
      $continue = FALSE;
    }
    $returnValues[] = $result;
  }
  return civicrm_api3_create_success($returnValues, $params, 'ContactRfm', 'Runqueue');
}
