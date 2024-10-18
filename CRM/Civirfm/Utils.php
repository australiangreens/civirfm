<?php

/**
 * This is the extension class that holds a lot of core extension functionality.
 * We define it here so it's easily accessible in a variety of places programmatically
 * and manually, via API and other methods.
 */
class CRM_Civirfm_Utils {
  /**
   * Callback function for CiviRFM queue/task processing.
   * Invokes self::calculateRFM() on the given contact_id.
   * 
   * @param CRM_Queue_TaskContext $ctx
   * @param array $params
   * @return bool
   */
  public static function processRFMTask(CRM_Queue_TaskContext $ctx, $params): bool {
    // Pass contact_id to self::calculateRFM()
    try {
      self::calculateRFM($params['contact_id']);
      return TRUE;
    } catch (Exception $e) {
      Civi::log()->error('Error calculating RFM values for contact {contact_id}', ['contact_id' => $params['contact_id'], 'error' => $e->getMessage()]);
      return FALSE;
    }
  }

  /**
   * Calculate the RFM values for the supplied contact_id.
   * If RFM values can be calculated, upserts the appropriate ContactRfm record.
   * 
   * @param int $contact_id
   * @return array
   */
  public static function calculateRFM($contact_id): array {
    // Get extension settings
    $rfm_period = Civi::settings()->get('civirfm_rfm_period');
    $fin_types = (Civi::settings()->get('civirfm_fin_types')) ? explode(',', Civi::settings()->get('civirfm_fin_types')) : NULL;

    // Construct the earliest date that defines our RFM period/window
    $rfm_earliest_date = new DateTime();
    $rfm_earliest_date->sub(new DateInterval('P' . $rfm_period . 'Y'));

    // Get all completed >$0 contribs of the right fin type(s),
    // or all fin types if none are defined in the extension settings
    $contribs = \Civi\Api4\Contribution::get(FALSE)
      ->addSelect('id', 'total_amount', 'receive_date')
      ->addWhere('contact_id', '=', $contact_id)
      ->addWhere('contribution_status_id:label', '=', 'Completed')
      ->addWhere('receive_date', '>', $rfm_earliest_date->format('Y-m-d H:i:sP'))
      ->addWhere('total_amount', '>', 0)
      ->addWhere('is_test', '=', 0)
      ->addOrderBy('receive_date', 'ASC')
      ->setLimit(0);
    if ($fin_types) {
      $contribs->addWhere('financial_type_id', 'IN', $fin_types);
    }
    $contribs = $contribs->execute();
    $contribs = iterator_to_array($contribs);

    // Set up our return array
    $result['recency'] = NULL;
    $result['frequency'] = NULL;
    $result['monetary'] = NULL;
    
    // If we have an empty array, we cannot calculate RFM values.
    // Delete any existing ContactRfm record and return.
    if (empty($contribs)) {
      \Civi\Api4\ContactRfm::delete(FALSE)
        ->addWhere('contact_id', '=', $contact_id)
        ->execute();
      return $result;
    }
    // Calculate RFM values
    $date_first = $contribs[0]['receive_date'];
    $date_last = $contribs[count($contribs)-1]['receive_date'];

    $recency = (new DateTime())->diff(new DateTime($date_last))->days;
    $frequency = count($contribs);
    $monetary = round(array_sum(array_column($contribs, 'total_amount')) / $frequency);

    $payload['contact_id'] = $contact_id;
    $payload['date_last_contrib'] = $date_last;
    $payload['date_first_contrib'] = $date_first;
    $payload['date_calculated'] = (new DateTimeImmutable())->format('Y-m-d H:i:sP');
    $payload['frequency'] = $frequency;
    $payload['monetary'] = $monetary;

    // Upsert ContactRfm record
    $res = \Civi\Api4\ContactRfm::save(FALSE)
      ->setRecords([$payload])
      ->setMatch(['contact_id'])
      ->execute();

    // Update $result and return
    $result['recency'] = $recency;
    $result['frequency'] = $frequency;
    $result['monetary'] = $monetary;
    return $result;
  }

  /**
   * Finds expired ContactRfm records and queues them for reprocessing.
   * "Expired" ContactRFM records are those where the date of the first contribution
   * is earlier than "now - rfm_period"
   * 
   * @return int
   */
  public static function refreshExpired(): int {
    $rfm_period = Civi::settings()->get('civirfm_rfm_period');
    $rfm_earliest_date = new DateTime();
    $rfm_earliest_date->sub(new DateInterval('P' . $rfm_period . 'Y'));

    $expiredRecords = \Civi\Api4\ContactRfm::get(FALSE)
      ->addSelect('contact_id')
      ->addWhere('date_first_contrib', '<', $rfm_earliest_date->format('Y-m-d H:i:sP'))
      ->setLimit(0)
      ->execute();

    if ($expiredRecords->rowCount) {
      $queue = CRM_Civirfm_Queue::singleton()->getQueue();
      foreach ($expiredRecords as $record) {
        $params = ['contact_id' => $record['contact_id']];
        $task = new CRM_Queue_Task(
          ['CRM_Civirfm_Utils', 'processRFMTask'],
          [$params]
        );
        $queue->createItem($task);
      }
    }
    return $expiredRecords->rowCount;
  }
}
