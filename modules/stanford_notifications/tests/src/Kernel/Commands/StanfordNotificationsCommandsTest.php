<?php

namespace Drupal\Tests\stanford_notifications\Kernel\Commands;

use Drupal\stanford_notifications\Commands\StanfordNotificationsCommands;
use Drupal\Tests\stanford_notifications\Kernel\StanfordNotificationTestBase;
use Drush\Exceptions\UserAbortException;

/**
 * Class StanfordNotificationsCommandsTest.
 *
 * @group stanford_notifications
 * @coversDefaultClass \Drupal\stanford_notifications\Commands\StanfordNotificationsCommands
 */
class StanfordNotificationsCommandsTest extends StanfordNotificationTestBase {

  /**
   * Drush command should make some entities.
   */
  public function testAddDrushCommand() {
    $this->createUser();
    $commands = new StanfordNotificationsCommands(\Drupal::service('notification_service'));
    $commands->addNotification('Foo Bar');
    $notifications = \Drupal::entityTypeManager()->getStorage('notification')->loadMultiple();
    $this->assertCount(1, $notifications);

    $this->expectException(UserAbortException::class);
    $commands->addNotification('Foo', ['status' => 'foo']);
  }

}
