<?php

namespace Drupal\Tests\stanford_publication\Kernel;

use Drupal\Core\Session\AccountInterface;
use Drupal\stanford_publication\Entity\Citation;

/**
 * Class CitationAccessControlHandlerTest.
 *
 * @group stanford_publication
 * @coversDefaultClass \Drupal\stanford_publication\CitationAccessControlHandler
 */
class CitationAccessControlHandlerTest extends PublicationTestBase {

  /**
   * Anonymous accounts only get to view the entity.
   */
  public function testAnonymousAccess() {
    $citation = Citation::create(['type' => 'su_book']);
    $citation->save();
    $access_handler = \Drupal::entityTypeManager()
      ->getAccessControlHandler('citation');

    $this->assertTrue($access_handler->access($citation, 'view'));
    $this->assertFalse($access_handler->access($citation, 'update'));
    $this->assertFalse($access_handler->access($citation, 'delete'));
    $this->assertFalse($access_handler->access($citation, 'foo'));

    $this->assertFalse($access_handler->createAccess('su_book'));
  }

  /**
   * Admins can view and update and delete the entities.
   */
  public function testAdminAccess() {
    $account = $this->createMock(AccountInterface::class);
    $account->method('hasPermission')->willReturn(TRUE);

    $citation = Citation::create(['type' => 'su_book']);
    $citation->save();
    $access_handler = \Drupal::entityTypeManager()
      ->getAccessControlHandler('citation');

    $this->assertTrue($access_handler->access($citation, 'view', $account));
    $this->assertTrue($access_handler->access($citation, 'update', $account));
    $this->assertTrue($access_handler->access($citation, 'delete', $account));
    $this->assertFalse($access_handler->access($citation, 'foo', $account));

    $this->assertTrue($access_handler->createAccess('su_book', $account));
  }

}
