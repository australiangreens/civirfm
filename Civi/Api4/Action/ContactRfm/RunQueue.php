<?php

namespace Civi\Api4\Action\ContactRfm;

use Civi\Api4\Generic\Result;
use CRM_Civirfm_ExtensionUtil as E;
use CRM_Civirfm_Queue;
use CRM_Queue_Runner;

/**
 * Process queued RFM jobs.
 * 
 * @method runQueue()
 */
class RunQueue extends \Civi\Api4\Generic\AbstractAction {

   public function _run(Result $result) {
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
      $output = $runner->runNext();
      if (!$output['is_continue']) {
        // all items in the queue are processed
        $continue = FALSE;
      }
      $result[] = $output;
   }
  }
}