<?php

namespace Drupal\Tests\stanford_publication\Kernel;

use Drupal\stanford_publication\Entity\CitationType;

/**
 * Access controller for the Citation type entity.
 *
 * @see \Drupal\stanford_publication\Entity\Citation.
 */
class CitationTypeAccessControlHandlerTest extends PublicationTestBase {

  /**
   * Anonymous accounts only get to view the entity.
   */
  public function testAnonymousAccess() {
    $citation_type = CitationType::load('su_book');

    $access_handler = \Drupal::entityTypeManager()
      ->getAccessControlHandler('citation_type');

    $this->assertTrue($access_handler->access($citation_type, 'view'));
    $this->assertFalse($access_handler->access($citation_type, 'update'));
    $this->assertFalse($access_handler->access($citation_type, 'delete'));
    $this->assertFalse($access_handler->access($citation_type, 'foo'));
  }

}
