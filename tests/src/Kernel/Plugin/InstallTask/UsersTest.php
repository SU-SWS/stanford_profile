<?php

namespace Drupal\Tests\stanford_profile\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\user\Entity\Role;

class UsersTest extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
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

  /**
   * The response guzzle mock object will return.
   *
   * @var mixed
   */
  protected $guzzleResponse;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->setInstallProfile('stanford_profile');

    $this->installEntitySchema('user');
    $this->installEntitySchema('user_role');
    $this->installEntitySchema('field_storage_config');
    $this->installEntitySchema('field_config');
    $this->installEntitySchema('config_pages_type');
    $this->installEntitySchema('config_pages');
    $this->installSchema('externalauth', 'authmap');
    $this->installSchema('system', ['key_value_expire', 'sequences']);
    $this->installConfig('system');

    Role::create(['label' => 'Owner', 'id' => "site_manager"])->save();

  }

  public function testUsers(){
    $this->assertTrue(TRUE);
  }


}
