<?php

namespace Civi\Api4\Action\ContactRfm;

use Civi\Api4\Generic\Result;
use CRM_Civirfm_Utils;

/**
 * Refresh expired RFM data.
 * 
 * @method refreshExpired()
 */
class RefreshExpired extends \Civi\Api4\Generic\AbstractAction {

   /**
    * Entry point function for this API action.
    *
    * @param \Civi\Api4\Generic\Result $result
    *
    * @return void
    */
   public function _run(Result $result): void {
      $count_expired_records = CRM_Civirfm_Utils::refreshExpired();
      $result[] = [
         'count' => $count_expired_records,
      ];
   }

   /**
    * Declare pseudo-fields for this API action.
    * As this action just queues CRM_Civirfm_Utils::calculateRMF() calls
    * we don't have any fields as such to return.
    * But we do want to tell the caller how many records we found for refreshing.
    *
    * @return array
    */
    public static function fields() {
      return [
         ['name' =>'count', 'data_type' => 'Integer']
      ];
    }

 }