<?php

namespace Civi\Api4\Action\Contact;

use Civi\Api4\Generic\Result;

/**
 * Calculate RFM values for the Contact. Uses CRM_Civirfm_Utils::calculateRFM
 * to upsert ContactRfm entity record.
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
    $data = \CRM_Civirfm_Utils::calculateRFM($this->contactId);
    $result[] = [
      'id' => $this->contactId,
      'recency' => $data['recency'],
      'frequency' => $data['frequency'],
      'monetary' => $data['monetary']
    ];
   }
}