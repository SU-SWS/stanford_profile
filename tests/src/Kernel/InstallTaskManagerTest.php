<?php

namespace Drupal\Tests\stanford_profile\Kernel;

use Drupal\KernelTests\KernelTestBase;

class InstallTaskManagerTest extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
    'user',
  ];

  protected function setUp() {
    parent::setUp();
    $this->setInstallProfile('stanford_profile');
  }

  public function testPluginManager(){
    \Drupal::service('plugin.manager.install_tasks');
  }

}
