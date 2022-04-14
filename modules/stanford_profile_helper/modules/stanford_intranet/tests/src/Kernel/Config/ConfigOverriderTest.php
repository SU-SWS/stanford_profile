<?php

namespace Drupal\Tests\stanford_intranet\Kernel\Config;

use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\stanford_intranet\Kernel\IntranetKernelTestBase;

/**
 * Class ConfigOverriderTest.
 *
 * @coversDefaultClass \Drupal\stanford_intranet\Config\ConfigOverrider
 */
class ConfigOverriderTest extends IntranetKernelTestBase {

  /**
   * File fields uri scheme should change to private.
   */
  public function testFileFieldConfigOverrides() {
    $this->assertEquals('public', $this->fieldStorage->getSetting('uri_scheme'));
    \Drupal::state()->set('stanford_intranet', TRUE);

    // Reload the field storage.
    $this->fieldStorage = FieldStorageConfig::load($this->fieldStorage->id());
    $this->assertEquals('private', $this->fieldStorage->getSetting('uri_scheme'));

    $this->assertNull(\Drupal::service('stanford_intranet.config_overrider')->createConfigObject('foo'));
  }

}
