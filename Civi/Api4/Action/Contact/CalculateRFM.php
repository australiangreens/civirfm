<?php

namespace Civi\Api4\Action\Contact;

use Civi\Api4\Generic\Result;

/**
 * Calculate RFM values for the Contact. Upserts a ContactRfm entity record for
 * the contact.
 * 
 * @method calculateRFM()
 */
class CalculateRFM extends \Civi\Api4\Generic\AbstractAction {

  /**
   * ID of contact
   * 
   * @var int
   * @required
   */
  protected $contactId;

   public function _run(Result $result) {
    // get extension settings

    // get Contact ID

    // do things, stuff

    // upsert ContactRfm record

    // update $result and return
   }
}