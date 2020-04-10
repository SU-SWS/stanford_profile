<?php

namespace Drupal\Tests\stanford_profile_drush\Kernel\Commands;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\Entity\ParagraphsType;
use Drupal\stanford_profile_drush\Commands\StanfordProfileCommands;

/**
 * Class StanfordProfileCommandsTest.
 *
 * @group stanford_profile
 * @coversDefaultClass \Drupal\stanford_profile_drush\Commands\StanfordProfileCommands
 */
class StanfordProfileCommandsTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'paragraphs',
    'react_paragraphs',
    'path_alias',
    'field',
    'file',
    'entity_reference_revisions',
    'entity_reference',
    'text',
    'link',
    'filter',
  ];

  /**
   * Command object.
   *
   * @var \Drupal\stanford_profile_drush\Commands\StanfordProfileCommands
   */
  protected $command;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('paragraph');

    NodeType::create(['type' => 'page'])->save();
    ParagraphsType::create([
      'label' => 'card',
      'id' => 'card',
    ])->save();
    $this->createFieldOnParagraphType('card');

    ParagraphsType::create([
      'label' => 'foo bar',
      'id' => 'foo_bar',
    ])->save();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_foo',
      'entity_type' => 'node',
      'type' => 'react_paragraphs',
    ]);
    $field_storage->save();

    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'page',
      'label' => $this->randomMachineName(),
      'settings' => [
        'handler_settings' => [
          'negate' => 1,
          'target_bundles_drag_drop' => [
            'card' => ['enabled' => FALSE],
            'foo_bar' => ['enabled' => TRUE],
          ],
        ],
      ],
    ])->save();

    $entityTypeManager = \Drupal::entityTypeManager();
    $bundleInfo = \Drupal::service('entity_type.bundle.info');
    $fieldManager = \Drupal::service('entity_field.manager');
    $fieldTypeManager = \Drupal::service('plugin.manager.field.field_type');
    $this->command = new StanfordProfileCommands($entityTypeManager, $bundleInfo, $fieldManager, $fieldTypeManager);
  }

  protected function createFieldOnParagraphType($bundle) {
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_bar_string',
      'entity_type' => 'paragraph',
      'type' => 'string',
    ]);
    $field_storage->save();
    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $bundle,
      'label' => $this->randomMachineName(),
    ])->save();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_bar_text_long',
      'entity_type' => 'paragraph',
      'type' => 'text_long',
    ]);
    $field_storage->save();
    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $bundle,
      'label' => $this->randomMachineName(),
    ])->save();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'field_bar_link',
      'entity_type' => 'paragraph',
      'type' => 'link',
    ]);
    $field_storage->save();
    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $bundle,
      'label' => $this->randomMachineName(),
    ])->save();
  }

  /**
   * The command should generate appropriate counts of fields.
   */
  public function testCommand() {
    $this->assertCount(0, Node::loadMultiple());
    $this->assertCount(0, Paragraph::loadMultiple());
    $this->command->generateStressTestNode();
    $this->assertCount(1, Node::loadMultiple());
    $this->assertCount(10, Paragraph::loadMultiple());
  }

}
