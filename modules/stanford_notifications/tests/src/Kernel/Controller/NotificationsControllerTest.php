<?php

namespace Drupal\Tests\stanford_notifications\Kernel\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\stanford_notifications\Controller\NotificationsController;
use Drupal\stanford_notifications\Entity\Notification;
use Drupal\Tests\stanford_notifications\Kernel\StanfordNotificationTestBase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class NotificationsControllerTest.
 *
 * @group stanford_notifications
 * @coversDefaultClass \Drupal\stanford_notifications\Controller\NotificationsController
 */
class NotificationsControllerTest extends StanfordNotificationTestBase {

  /**
   * Incorrect users get access denied.
   */
  public function test403Callback() {
    $notification = Notification::create([
      'message' => 'foo',
      'uid' => 12,
      'status' => 'warning',
    ]);
    $notification->save();

    $controller = NotificationsController::create(\Drupal::getContainer());

    $this->expectException(AccessDeniedHttpException::class);
    $controller->clearNotification($notification);
  }

  /**
   * The correct user will delete the entity.
   */
  public function testCorrectUser() {
    $user = $this->createUser();
    $notification = Notification::create([
      'message' => 'foo',
      'uid' => $user->id(),
      'status' => 'warning',
    ]);
    $notification->save();

    \Drupal::currentUser()->setAccount($user);
    $controller = NotificationsController::create(\Drupal::getContainer());
    $response = $controller->clearNotification($notification);

    $this->assertInstanceOf(AjaxResponse::class, $response);
    $commands = $response->getCommands();
    $this->assertEqual($commands[0]['selector'], '[data-notification-id="' . $notification->id() . '"]');
    $this->assertEqual($commands[1]['selector'], '.toolbar-icon-notifications');
  }

}
