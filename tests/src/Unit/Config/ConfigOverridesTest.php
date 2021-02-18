<?php

namespace Drupal\Tests\stanford_profile\Unit\Config;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\State\StateInterface;
use Drupal\stanford_profile\Config\ConfigOverrides;
use Drupal\Tests\UnitTestCase;

/**
 * Class ConfigOverridesTest
 *
 * @group stanford_profile
 * @coversDefaultClass \Drupal\stanford_profile\Config\ConfigOverrides
 */
class ConfigOverridesTest extends UnitTestCase {

  /**
   * @var \Drupal\stanford_profile\Config\ConfigOverrides
   */
  protected $overrideService;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $state = $this->createMock(StateInterface::class);
    $state->method('get')->will($this->returnCallback([
      $this,
      'getStateCallback',
    ]));

    $config_factory = $this->createMock(ConfigFactoryInterface::class);
    $config_factory->method('getEditable')
      ->will($this->returnCallback([$this, 'getConfigCallback']));

    $this->overrideService = new ConfigOverrides($state, $config_factory);
  }

  public function testConfigOverrides() {
    $this->assertEquals('StanfordProfileConfigOverride', $this->overrideService->getCacheSuffix());
    $this->assertNull($this->overrideService->createConfigObject('foo'));
    $this->assertInstanceOf(CacheableMetadata::class, $this->overrideService->getCacheableMetadata('foo'));

    $overrides = $this->overrideService->loadOverrides(['system.site']);
    $this->assertEquals([
      'page' => [
        403 => '/node/403',
        404 => '/node/404',
        'front' => '/node/99',
      ],
    ], $overrides['system.site']);
  }

  /**
   * Test the config ignore settings overrides.
   */
  public function testConfigIgnoreOverrides() {
    $overrides = $this->overrideService->loadOverrides(['config_ignore.settings']);
    $expected = [
      'config_ignore.settings' => [
        'ignored_config_entities' => ['stable.settings', 'seven.settings'],
      ],
    ];
    $this->assertEquals($expected, $overrides);
  }

  /**
   * Google Tag manager entities should be disabled.
   */
  public function testGoogleTagOverrides() {
    $overrides = $this->overrideService->loadOverrides(['google_tag.container.foo_bar']);
    $expected = ['google_tag.container.foo_bar' => ['status' => FALSE]];
    $this->assertEquals($expected, $overrides);
  }

  /**
   * State callback.
   */
  public function getStateCallback($name) {
    switch ($name) {
      case 'stanford_profile.403_page':
        return '/node/403';

      case 'stanford_profile.404_page':
        return '/node/404';

      case 'stanford_profile.front_page':
        return '/node/99';

    }
  }

  public function getConfigCallback($name) {
    $config = $this->createMock(Config::class);
    $setting = [];
    switch ($name) {
      case 'core.extension':
        $setting = ['stable' => 0, 'seven' => 0];
        break;
    }

    $config->method('getOriginal')->willReturn($setting);
    return $config;
  }

  /**
   * During installation, the config ignore settings shouldn't contain anything.
   */
  public function testConfigOverridesDuringInstall(){
    $GLOBALS['install_state'] = true;

    $overrides = $this->overrideService->loadOverrides(['config_ignore.settings']);
    $expected = [
      'config_ignore.settings' => [
        'ignored_config_entities' => ['foo', 'foo'],
      ],
    ];
    $this->assertEquals($expected, $overrides);
  }

}
