<?php

namespace Drupal\Tests\stanford_notifications\Kernel\Entity;

use Drupal\stanford_notifications\Entity\Notification;
use Drupal\Tests\stanford_notifications\Kernel\StanfordNotificationTestBase;

/**
 * Class NotificationTest.
 *
 * @group stanford_notifications
 * @coversDefaultClass \Drupal\stanford_notifications\Entity\Notification
 */
class NotificationTest extends StanfordNotificationTestBase {

  /**
   * Test the basic methods.
   */
  public function testEntityMethods() {
    $entity = Notification::create([
      'message' => 'foo',
      'uid' => 123,
      'status' => 'foo-bar',
    ]);
    $entity->save();

    $this->assertEquals(123, $entity->userId());
    $this->assertEquals('foo', $entity->message());
    $this->assertEquals('foo-bar', $entity->status());
  }

}
