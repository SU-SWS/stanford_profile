<?php

namespace Drupal\stanford_notifications\Commands;

use Drupal\Core\Messenger\Messenger;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\stanford_notifications\NotificationServiceInterface;
use Drush\Commands\DrushCommands;
use Drush\Exceptions\UserAbortException;

/**
 * Class StanfordNotificationsCommands.
 *
 * @package Drupal\stanford_notifications\Commands
 */
class StanfordNotificationsCommands extends DrushCommands {

  /**
   * Notification service.
   *
   * @var \Drupal\stanford_notifications\NotificationServiceInterface
   */
  protected $notificationService;

  /**
   * StanfordNotificationsCommands constructor.
   *
   * @param \Drupal\stanford_notifications\NotificationServiceInterface $notificationService
   *   Notification service.
   */
  public function __construct(NotificationServiceInterface $notificationService) {
    $this->notificationService = $notificationService;
  }

  /**
   * Add a new notification to users on the site.
   *
   * @param string $message
   *   Notification message for the user.
   * @param array $options
   *   Keyed array of options.
   *
   * @command stanford:add-notification
   * @options roles Comma delimited list of roles to set the notification for.
   * @options status Notification status type, 'status', 'warning', or 'error'.
   *
   * @throws \Drush\Exceptions\UserAbortException
   */
  public function addNotification($message, array $options = [
    'roles' => '',
    'status' => Messenger::TYPE_STATUS,
  ]) {
    $status_options = [
      Messenger::TYPE_STATUS,
      Messenger::TYPE_WARNING,
      Messenger::TYPE_ERROR,
    ];

    if (!in_array($options['status'], $status_options)) {
      throw new UserAbortException(new TranslatableMarkup('Invalid status. Please use one of the options: @options', ['@options' => implode(', ', $status_options)]));
    }
    $roles = explode(',', $options['roles']);
    array_walk($roles, 'trim');
    $this->notificationService->addNotification($message, array_filter($roles), $options['status']);
  }

}
