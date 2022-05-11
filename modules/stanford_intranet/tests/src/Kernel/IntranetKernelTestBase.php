<?php

namespace Drupal\Tests\stanford_intranet\Kernel;

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;

abstract class IntranetKernelTestBase extends KernelTestBase {

  /**
   * @var \Drupal\field\FieldStorageConfigInterface
   */
  protected $fieldStorage;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'field',
    'node',
    'file',
    'user',
    'config_pages',
    'stanford_profile_helper',
    'options',
    'image',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('file');
    $this->installEntitySchema('image_style');
    $this->installConfig('system');
    $this->installSchema('system', ['sequences']);
    $this->installSchema('node', ['node_access']);
    $this->installSchema('file', ['file_usage']);

    $this->fieldStorage = FieldStorageConfig::create([
      'field_name' => 'field_foo',
      'entity_type' => 'node',
      'type' => 'file',
    ]);
    $this->fieldStorage->save();

    NodeType::create(['type' => 'page'])->save();

    \Drupal::service('module_installer')->install(['stanford_intranet']);
  }

}
