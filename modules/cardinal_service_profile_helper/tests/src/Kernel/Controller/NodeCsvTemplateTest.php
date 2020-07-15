<?php

namespace Drupal\Tests\cardinal_service_profile_helper\Kernel\Controller;

use Drupal\cardinal_service_profile_helper\Controller\NodeCsvTemplate;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;

/**
 * Class NodeCsvTemplateTest
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_profile_helper\Controller\NodeCsvTemplate
 */
class NodeCsvTemplateTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'cardinal_service_profile_helper',
    'node',
    'field',
    'user',
    'text',
    'link',
    'entity_reference',
  ];

  /**
   * Generated node type.
   *
   * @var \Drupal\node\NodeTypeInterface
   */
  protected $nodeType;

  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->nodeType = NodeType::create(['type' => 'page', 'name' => 'page']);
    $this->nodeType->save();

    $fieldStorage = FieldStorageConfig::create([
      'field_name' => 'field_foo',
      'entity_type' => 'node',
      'type' => 'string',
    ]);
    $fieldStorage->save();
    $field = FieldConfig::create([
      'field_storage' => $fieldStorage,
      'bundle' => 'page',
    ])->save();

    $fieldStorage = FieldStorageConfig::create([
      'field_name' => 'field_bar',
      'entity_type' => 'node',
      'type' => 'link',
    ]);
    $fieldStorage->save();
    $field = FieldConfig::create([
      'field_storage' => $fieldStorage,
      'bundle' => 'page',
    ])->save();

    $fieldStorage = FieldStorageConfig::create([
      'field_name' => 'field_baz',
      'entity_type' => 'node',
      'type' => 'entity_reference',
    ]);
    $fieldStorage->save();
    $field = FieldConfig::create([
      'field_storage' => $fieldStorage,
      'bundle' => 'page',
    ])->save();
  }

  /**
   * The controller should provide a CSV file.
   */
  public function testTemplateController() {
    $controller = NodeCsvTemplate::create(\Drupal::getContainer());
    $response = $controller->getTemplate($this->nodeType);

    $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
    $this->assertEqual('title,field_foo,field_bar|uri,field_bar|title', file_get_contents('temporary://page.csv'));
  }

}
