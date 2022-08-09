<?php

namespace Drupal\Tests\stanford_publication\Kernel;

use Drupal\Core\Session\AccountInterface;
use Drupal\stanford_publication\Entity\CitationType;

/**
 * Class CitationTypeListBuilderTest
 *
 * @group stanford_publication
 * @coversDefaultClass \Drupal\stanford_publication\CitationTypeListBuilder
 */
class CitationTypeListBuilderTest extends PublicationTestBase {

  /**
   * Citation Type list builder has some header and rows.
   */
  public function testListBuilder() {
    $list_builder = \Drupal::entityTypeManager()
      ->getListBuilder('citation_type');
    $header = $list_builder->buildHeader();
    $this->assertArrayHasKey('label', $header);
    $this->assertArrayHasKey('id', $header);
    $this->assertArrayHasKey('operations', $header);

    $citation_type = CitationType::load('su_book');
    $row = $list_builder->buildRow($citation_type);
    $this->assertArrayHasKey('label', $row);
    $this->assertArrayHasKey('id', $row);
    $this->assertArrayHasKey('operations', $row);

    $account = $this->createMock(AccountInterface::class);
    $account->method('hasPermission')->willReturn(TRUE);
    \Drupal::currentUser()->setAccount($account);

    $operations = $list_builder->getDefaultOperations($citation_type);
    $this->assertEquals(30, $operations['edit']['weight']);
  }

}
