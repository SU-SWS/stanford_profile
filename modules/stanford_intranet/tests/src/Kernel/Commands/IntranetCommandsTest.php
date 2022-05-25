<?php

namespace Drupal\Tests\stanford_intranet\Kernel\Commands;

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\config_pages\Entity\ConfigPagesType;
use Drupal\externalauth\AuthmapInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\stanford_intranet\Commands\IntranetCommands;
use Drupal\Tests\stanford_intranet\Kernel\IntranetKernelTestBase;

/**
 * Drush commands tests.
 *
 * @coversDefaultClass \Drupal\stanford_intranet\Commands\IntranetCommands
 */
class IntranetCommandsTest extends IntranetKernelTestBase {

  /**
   * Drush commands.
   *
   * @var \Drupal\stanford_intranet\Commands\IntranetCommands
   */
  protected $commands;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('config_pages');
    ConfigPagesType::create([
      'id' => 'stanford_saml',
      'context' => [],
      'menu' => [],
    ])->save();

    FieldStorageConfig::create([
      'field_name' => 'su_simplesaml_affil',
      'type' => 'list_string',
      'entity_type' => 'config_pages',
      'cardinality' => -1
    ])->save();
    FieldConfig::create([
      'field_name' => 'su_simplesaml_affil',
      'entity_type' => 'config_pages',
      'bundle' => 'stanford_saml',

    ])->save();

    FieldStorageConfig::create([
      'field_name' => 'su_simplesaml_users',
      'type' => 'string',
      'entity_type' => 'config_pages',
      'cardinality' => -1
    ])->save();
    FieldConfig::create([
      'field_name' => 'su_simplesaml_users',
      'entity_type' => 'config_pages',
      'bundle' => 'stanford_saml',
    ])->save();

    FieldStorageConfig::create([
      'field_name' => 'su_simplesaml_allowed',
      'type' => 'string',
      'entity_type' => 'config_pages',
      'cardinality' => -1
    ])->save();
    FieldConfig::create([
      'field_name' => 'su_simplesaml_allowed',
      'entity_type' => 'config_pages',
      'bundle' => 'stanford_saml',
    ])->save();

    FieldStorageConfig::create([
      'field_name' => 'su_simplesaml_roles',
      'type' => 'string_long',
      'entity_type' => 'config_pages',
    ])->save();
    FieldConfig::create([
      'field_name' => 'su_simplesaml_roles',
      'entity_type' => 'config_pages',
      'bundle' => 'stanford_saml',
    ])->save();

    $ext_auth = $this->createMock(AuthmapInterface::class);
    $this->commands = new IntranetCommands(\Drupal::entityTypeManager(), \Drupal::state(), $ext_auth, \Drupal::service('password_generator'), \Drupal::service('stanford_intranet.manager'));
  }

  public function testIntranetSetup() {
    $options = [
      'users' => 'Foo,Foo',
      'roles' => 'Bar,Bar',
      'affiliations' => 'staff,faculty',
      'workgroups' => 'foo:bar,bar:foo',
      'role-mapping' => 'foo:bar=Bar,bar=Baz',
    ];
    $this->commands->setupIntranet($options);
    $this->assertTrue((bool) \Drupal::state()->get('stanford_intranet'));

    $this->assertNotEmpty(\Drupal::entityTypeManager()
      ->getStorage('user')
      ->loadByProperties(['name' => 'Foo']));

    $this->assertNotEmpty(\Drupal::entityTypeManager()
      ->getStorage('user_role')
      ->loadByProperties(['label' => 'Bar']));

    /** @var \Drupal\config_pages\ConfigPagesInterface $config_page */
    $config_page = ConfigPages::load('stanford_saml');

    $affil = $config_page->get('su_simplesaml_affil')->getString();
    $this->assertStringContainsString('staff', $affil);
    $this->assertStringContainsString('faculty', $affil);

    $allowed_users = $config_page->get('su_simplesaml_users')->getString();
    $this->assertEquals('Foo', $allowed_users);

    $allowed_users = $config_page->get('su_simplesaml_users')->getString();
    $this->assertEquals('Foo', $allowed_users);
  }

}
