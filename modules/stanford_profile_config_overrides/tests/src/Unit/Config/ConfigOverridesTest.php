<?php

namespace Drupal\Tests\stanford_profile_config_overrides\Unit\Config;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\stanford_profile_config_overrides\Config\ConfigOverrides;
use Drupal\Tests\UnitTestCase;

/**
 * Class ConfigOverridesTest
 *
 * @coversDefaultClass \Drupal\stanford_profile_config_overrides\Config\ConfigOverrides
 * @group stanford_profile
 */
class ConfigOverridesTest extends UnitTestCase {

  /**
   * Service object to test.
   *
   * @var \Drupal\stanford_profile_config_overrides\Config\ConfigOverrides
   */
  protected $configOverrides;

  /**
   * Keyed array of values the config pages service will return.
   *
   * @var array
   */
  protected $configPagesValues = [];

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $mock_config_pages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $mock_config_pages->method('getValue')
      ->will($this->returnCallback([$this, 'getConfigPagesValueCallback']));
    $this->configOverrides = new ConfigOverrides($mock_config_pages);
  }

  /**
   * Test the methods that dont do much.
   */
  public function testSimpleMethods() {
    $this->assertEmpty($this->configOverrides->createConfigObject('', ''));
    $this->assertEquals('ConfigOverrides', $this->configOverrides->getCacheSuffix());
    $this->assertInstanceOf('Drupal\Core\Cache\CacheableMetadata', $this->configOverrides->getCacheableMetadata(''));
  }

  /**
   * Test Domain 301 redirect config overrides.
   */
  public function testDomain301Redirect() {
    $this->assertEmpty($this->configOverrides->loadOverrides([]));
    $expected_array = [
      'domain_301_redirect.settings' => [
        'enabled' => FALSE,
        'domain' => '',
      ],
    ];
    $this->assertArrayEquals($expected_array, $this->configOverrides->loadOverrides(['domain_301_redirect.settings']));

    $this->configPagesValues['stanford_basic_site_settings'] = ['su_site_url' => 'http://foo.bar'];
    $expected_array = [
      'domain_301_redirect.settings' => [
        'enabled' => TRUE,
        'domain' => 'https://foo.bar',
      ],
    ];
    $this->assertArrayEquals($expected_array, $this->configOverrides->loadOverrides(['domain_301_redirect.settings']));
  }

  /**
   * Test Brand Options config overrides.
   */
  public function testBrandOptions() {
    $this->assertEmpty($this->configOverrides->loadOverrides([]));
    $expected_array = [
      'system.site' => [
        'name' => 'University',
        'mail' => 'no-reply@stanford.edu',
        'slogan' => '',
        'page' => [
          '403' => '/node/3',
          '404' => '/node/2',
          'front' => '/node/1',
        ],
      ],
    ];
    $this->assertArrayEquals($expected_array, $this->configOverrides->loadOverrides(['system.site']));

    $this->configPagesValues['stanford_branding_options'] = ['su_site_name' => 'Go Hawks!'];
    $expected_array = [
      'system.site' => [
        'name' => 'Go Hawks!',
        'mail' => 'no-reply@stanford.edu',
        'slogan' => '',
        'page' => {
          '403' => '/node/3',
          '404' => '/node/2',
          'front' => '/node/1',
        },
      ],
    ];
    $this->assertArrayEquals($expected_array, $this->configOverrides->loadOverrides(['system.site']));
  }

  /**
   * Mock config pages service value callback.
   */
  public function getConfigPagesValueCallback($type, $field_name) {
    if (isset($this->configPagesValues[$type])) {
      return $this->configPagesValues[$type][$field_name];
    }
  }

}
