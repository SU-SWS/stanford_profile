<?php

namespace Drupal\Tests\stanford_publication\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;

/**
 * Class PublicationTestBase.
 *
 * @group stanford_publication
 * @coversDefaultClass \Drupal\stanford_publication\Entity\Citation
 */
abstract class PublicationTestBase extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
    'field',
    'stanford_publication',
    'test_stanford_publication_citations',
    'link',
    'name',
    'user',
    'node',
  ];

  /**
   * Parent node.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $parentNode;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installEntitySchema('citation');
    $this->installConfig('test_stanford_publication_citations');
    NodeType::create(['type' => 'page'])->save();
    $this->parentNode = Node::create(['title' => 'page', 'type' => 'page']);
    $this->parentNode->save();
  }

}
