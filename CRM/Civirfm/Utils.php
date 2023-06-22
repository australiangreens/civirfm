<?php

/**
 * This is the extension class that holds a lot of core extension functionality.
 * We define it here so it's easily accessible in a variety of places programmatically
 * and manually, via API and other methods.
 */
class CRM_Civirfm_Utils {
  public static function processRFMTask(CRM_Queue_TaskContext $ctx, $params) {
    // pass contact_id to self::calculateRFM()
    self::calculateRFM($params['contact_id']);
  }

  public static function calculateRFM($contact_id) {
    // get extension settings
    $rfm_period = Civi::settings()->get('civirfm_rfm_period');
    $fin_types = explode(',', Civi::settings()->get('civirfm_fin_types'));

    // Construct the earliest date that defines our RFM period/window
    $rfm_earliest_date = new DateTime();
    $rfm_earliest_date->sub(new DateInterval('P' . $rfm_period . 'Y'));

    // get all completed contribs of the right fin type(s)
    $contribs = \Civi\Api4\Contribution::get(FALSE)
      ->addSelect('id', 'total_amount', 'receive_date')
      ->addWhere('contact_id', '=', $contact_id)
      ->addWhere('contribution_status_id:label', '=', 'Completed')
      ->addWhere('financial_type_id', 'IN', $fin_types)
      ->addWhere('receive_date', '>', $rfm_earliest_date->format('Y-m-d H:i:sP'))
      ->addOrderBy('receive_date', 'ASC')
      ->setLimit(0)
      ->execute();
    $contribs = iterator_to_array($contribs);
    
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

    // upsert ContactRfm record
    $res = \Civi\Api4\ContactRfm::save(FALSE)
      ->setRecords([$payload])
      ->setMatch(['contact_id'])
      ->execute();

    // update $result and return
    $result['recency'] = $recency;
    $result['frequency'] = $frequency;
    $result['monetary'] = $monetary;
    return $result;
  }
}