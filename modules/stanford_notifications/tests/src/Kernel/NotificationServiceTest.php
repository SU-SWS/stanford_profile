<?php

namespace Drupal\Tests\stanford_notifications\Kernel;

use Drupal\Core\Messenger\Messenger;
use Drupal\Core\Session\AccountProxy;
use Drupal\stanford_notifications\Entity\Notification;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class NotificationServiceTest.
 *
 * @group stanford_notifications
 * @coversDefaultClass \Drupal\stanford_notifications\NotificationService
 */
class NotificationServiceTest extends StanfordNotificationTestBase {

  /**
   * @var \Drupal\stanford_notifications\NotificationServiceInterface
   */
  protected $service;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->service = \Drupal::service('notification_service');
  }

  /**
   * Test the add notification method creates appropriate entities.
   */
  public function testAddNotification() {
    $this->service->addNotification('Foo Bar', ['foo'], Messenger::TYPE_WARNING);
    $entities = Notification::loadMultiple();
    $this->assertCount(0, $entities);

    $this->createUser();

    $this->service->addNotification('Foo Bar', ['foo'], Messenger::TYPE_WARNING);
    $entities = Notification::loadMultiple();
    $this->assertCount(0, $entities);

    $user = $this->createUser('foo');

    $this->service->addNotification('Foo Bar', ['foo'], Messenger::TYPE_WARNING);
    $entities = Notification::loadMultiple();
    $this->assertCount(1, $entities);

    $event_dispatcher = $this->createMock(EventDispatcherInterface::class);
    $account = new AccountProxy($event_dispatcher);
    $account->setAccount($user);

    $this->service->clearUserNotifications($account);
    $entities = Notification::loadMultiple();
    $this->assertCount(0, $entities);
  }

  /**
   * Toolbar should contain a defined number of items.
   */
  public function testToolbar() {
    $user = $this->createUser('foo');
    \Drupal::currentUser()->setAccount($user);

    $this->service->addNotification('Foo Bar');
    $toolbar = $this->service->toolbar();
    $this->assertCount(1, $toolbar['notifications']['tray']['#items']);
  }

}
