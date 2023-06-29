<?php
namespace Civi\Api4;

/**
 * ContactRfm entity.
 *
 * Provided by the civirfm extension.
 *
 * @package Civi\Api4
 */
class ContactRfm extends Generic\DAOEntity {

  /**
   * @param bool $checkPermissions
   * @return Action\ContactRfm\RefreshExpired
   */
  public static function refreshExpired($checkPermissions = TRUE) {
    return (new Action\ContactRfm\RefreshExpired(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }

  /**
   * @param bool $checkPermissions
   * @return Action\ContactRfm\RunQueue
   */
  public static function runQueue($checkPermissions = TRUE) {
    return (new Action\ContactRfm\RunQueue(__CLASS__, __FUNCTION__))
      ->setCheckPermissions($checkPermissions);
  }
}
