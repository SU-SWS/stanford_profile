<?php

namespace Drupal\stanford_notifications;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Notification entity type interface.
 *
 * @package Drupal\stanford_notifications
 */
interface NotificationInterface extends ContentEntityInterface {

  /**
   * Get the notification message.
   *
   * @return string
   *   Text of the message, can include html.
   */
  public function message();

  /**
   * Get the user ID that the notification is for.
   *
   * @return int
   *   User entity id.
   */
  public function userId();

  /**
   * Get the status of the notification.
   *
   * @return string
   *   Status string.
   *
   * @see \Drupal\Core\Messenger\Messenger::TYPE_STATUS
   */
  public function status();

}
