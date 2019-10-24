<?php

namespace Drupal\Tests\stanford_profile\Kernel;

use Drupal\config_pages\Entity\ConfigPagesType;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;

class InstallTasksTest extends KernelTestBase {

  protected static $modules = [
    'system',
    'config_pages',
    'config_pages_overrides',
    'externalauth',
    'simplesamlphp_auth',
    'stanford_ssp',
    'user',
    'field',
  ];

  protected function setUp() {
    parent::setUp();
    $this->setInstallProfile('stanford_profile');
    drupal_flush_all_caches();
    $this->installEntitySchema('user');
    $this->installEntitySchema('field_storage_config');
    $this->installEntitySchema('field_config');
    $this->installEntitySchema('config_pages_type');
    $this->installEntitySchema('config_pages');
    ConfigPagesType::create([
      'id' => 'stanford_basic_site_settings',
      'menu' => [],
      'context' => [],
    ])->save();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'su_site_email',
      'entity_type' => 'config_pages',
      'type' => 'email',
    ]);
    $field_storage->save();
    FieldConfig::create([
      'entity_type' => 'config_pages',
      'field_storage' => $field_storage,
      'bundle' => 'stanford_basic_site_settings',
      'label' => 'Email',
    ])->save();

    $field_storage = FieldStorageConfig::create([
      'field_name' => 'su_site_name',
      'entity_type' => 'config_pages',
      'type' => 'string',
    ]);
    $field_storage->save();
    FieldConfig::create([
      'entity_type' => 'config_pages',
      'field_storage' => $field_storage,
      'bundle' => 'stanford_basic_site_settings',
      'label' => 'Name',
    ])->save();
  }

  public function testInstallTasks() {
    /** @var \Drupal\stanford_profile\InstallTasksInterface $service */
    $service = \Drupal::service('stanford_profile.install_tasks');
    $service->setSiteSettings('foo bar');

    $this->assertEquals('foo bar', \Drupal::config('system.site')->get('name'));
    $this->assertEquals('foo@bar.com', \Drupal::config('system.site')
      ->get('mail'));
  }

}
