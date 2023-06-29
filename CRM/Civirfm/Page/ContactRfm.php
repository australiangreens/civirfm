<?php
use CRM_Civirfm_ExtensionUtil as E;

class CRM_Civirfm_Page_ContactRfm extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts('RFM'));

    $contactId = CRM_Utils_Request::retrieve('cid', 'Positive', $this, TRUE);

    $contactRfm = \Civi\Api4\ContactRfm::get(FALSE)
      ->addSelect('id', 'contact_id', 'recency', 'frequency', 'monetary', 'date_calculated')
      ->addWhere('contact_id', '=', $contactId)
      ->execute()
      ->first(); // we can safely assume there is only a single ContactRfm record per contact

    if (isset($contactRfm['date_calculated'])) {
      $this->assign('contactId', $contactId);
      $this->assign('recency', $contactRfm['recency']);
      $this->assign('frequency', $contactRfm['frequency']);
      $this->assign('monetary', $contactRfm['monetary']);
      $this->assign('date_calc', $contactRfm['date_calculated']);
      $this->assign('rfm_time', \Civi::settings()->get('civirfm_rfm_period'));
      $this->assign('curr_symbol', CRM_Core_Config::singleton()->defaultCurrencySymbol);
    }

    // Set the user context
    $session = CRM_Core_Session::singleton();
    $userContext = CRM_Utils_System::url('civicrm/contact/view', 'cid=' . $contactId . '&selectedChild=contact_rfm&reset=1');
    $session->pushUserContext($userContext);

    parent::run();
  }

}
