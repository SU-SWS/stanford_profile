<?php

namespace Drupal\stanford_notifications;

use Drupal\Component\Utility\Html;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;

/**
 * Notification entity helper service.
 *
 * @package Drupal\stanford_notifications
 */
class NotificationService implements NotificationServiceInterface {

  use StringTranslationTrait;

  /**
   * Current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * NotificationService constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   Current user account.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   */
  public function __construct(AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function toolbar() {
    $notification_list = $this->getToolbarTrayItems($this->getUserNotifications());

    $items['notifications'] = [
      '#type' => 'toolbar_item',
      '#weight' => 999,
      'tab' => [
        '#type' => 'link',
        '#title' => $this->t('Notifications'),
        '#url' => Url::fromRoute('<front>'),
        '#attributes' => [
          'title' => $this->t('Notifications'),
          'class' => ['toolbar-icon', 'toolbar-icon-notifications'],
          'data-notification-count' => count($notification_list),
        ],
      ],
      'tray' => [
        '#theme' => 'item_list',
        '#items' => $notification_list,
        '#attributes' => ['class' => ['notification-list']],
      ],
      '#cache' => [
        'keys' => [$this->currentUser->id()],
        'tags' => ['notifications:' . $this->currentUser->id()],
      ],
      '#attached' => [
        'library' => [
          'stanford_notifications/notifications',
        ],
      ],
    ];
    return $items;
  }

  /**
   * Get a list render array of the given notifications for the toolbar tray.
   *
   * @param \Drupal\stanford_notifications\Entity\Notification[] $notifications
   *   Array of notifications to build the list.
   *
   * @return array
   *   Array of list render items for the tray.
   */
  protected function getToolbarTrayItems(array $notifications) {
    $notification_list = [];
    foreach ($notifications as $notification) {
      $clear_link = Link::createFromRoute($this->t('Delete'), 'stanford_notifications.clear', ['notification' => $notification->id()], ['attributes' => ['class' => ['use-ajax']]]);

      $status = Html::cleanCssIdentifier($notification->status());

      $notification_list[] = [
        '#wrapper_attributes' => [
          'data-notification-id' => $notification->id(),
          'class' => [
            'su-alert',
            'su-alert--' . ($status == 'status' ? 'success' : $status),
          ],
        ],
        'message' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $notification->message() . $clear_link->toString(),
          '#attributes' => ['class' => ['su-alert__body']],
        ],
        '#cache' => ['tags' => ['notification:' . $notification->id()]],
      ];

      // Light text for those with dark backgrounds.
      if ($status == "status") {
        $notification_list[count($notification_list) - 1]['#wrapper_attributes']['class'][] = 'su-alert--text-light';
      }

    }
    return $notification_list;
  }

  /**
   * {@inheritDoc}
   */
  public function addNotification($message, array $roles = [], $status = Messenger::TYPE_STATUS) {
    $user_query = $this->entityTypeManager->getStorage('user')->getQuery();
    if ($roles) {
      $user_query->condition('roles', $roles, 'IN');
    }

    $notification_storage = $this->entityTypeManager->getStorage('notification');
    $tags_to_invalidate = [];
    foreach ($user_query->execute() as $user_id) {
      $notification_storage->create([
        'message' => $message,
        'uid' => $user_id,
        'status' => $status,
      ])->save();
      $tags_to_invalidate[] = "notifications:$user_id";
    }
    Cache::invalidateTags(array_unique($tags_to_invalidate));
  }

  /**
   * {@inheritDoc}
   */
  public function getUserNotifications(AccountInterface $account = NULL) {
    if (!$account) {
      $account = $this->currentUser;
    }
    return $this->entityTypeManager->getStorage('notification')
      ->loadByProperties(['uid' => $account->id()]);
  }

  /**
   * {@inheritDoc}
   */
  public function clearUserNotifications(AccountInterface $account = NULL) {
    foreach ($this->getUserNotifications($account) as $notification) {
      $notification->delete();
    }
  }

}
