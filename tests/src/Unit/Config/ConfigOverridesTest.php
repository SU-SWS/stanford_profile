<?php

namespace Drupal\Tests\stanford_profile\Unit\Config;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\State\StateInterface;
use Drupal\stanford_profile\Config\ConfigOverrides;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests the Stanford profile config overrides service.
 */
#[Group('stanford_profile')]
class ConfigOverridesTest extends UnitTestCase {

  /**
   * Config overrides service being tested.
   *
   * @var \Drupal\stanford_profile\Config\ConfigOverrides
   */
  protected $overrideService;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $state = $this->createMock(StateInterface::class);
    $state->method('get')
      ->willReturnCallback([$this, 'getStateCallback']);

    $config_factory = $this->createMock(ConfigFactoryInterface::class);
    $config_factory->method('getEditable')
      ->willReturnCallback([$this, 'getConfigCallback']);

    $this->overrideService = new ConfigOverrides($state, $config_factory);

    $config_page_loader = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $config_page_loader->method('getValue')
      ->willReturnCallback([$this, 'getConfigPageValue']);

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $config_page_loader);
    \Drupal::setContainer($container);
  }

  /**
   * Test the system site pages are overridden from state.
   */
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
    // Fake like it's during installation time.
    $GLOBALS['install_state'] = TRUE;
    $overrides = $this->overrideService->loadOverrides(['config_ignore.settings']);
    $expected = [
      'config_ignore.settings' => [
        'ignored_config_entities' => ['foo', 'foo'],
      ],
    ];
    $this->assertEquals($expected, $overrides);

    // Flip back to not during install.
    unset($GLOBALS['install_state']);
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
   * Test SAML role mappings are overridden from the config page value.
   */
  public function testSamlOverrides() {
    $overrides = $this->overrideService->loadOverrides(['stanford_samlauth.settings']);
    $expected = [
      'stanford_samlauth.settings' => [
        'role_mapping' => [
          'mapping' => [
            ['role' => 'foo', 'attribute' => 'bar', 'value' => 'baz:bin'],
          ],
        ],
      ],
    ];
    $this->assertEquals($expected, $overrides);
  }

  /**
   * State callback.
   *
   * @param string $name
   *   State key.
   *
   * @return string|null
   *   Node path for the key, or NULL if not set.
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

  /**
   * Config factory callback returning a mocked config object.
   *
   * @param string $name
   *   Config name.
   *
   * @return \Drupal\Core\Config\Config
   *   Mocked config object.
   */
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
  public function testConfigOverridesDuringInstall() {
    $GLOBALS['install_state'] = TRUE;

    $overrides = $this->overrideService->loadOverrides(['config_ignore.settings']);
    $expected = [
      'config_ignore.settings' => [
        'ignored_config_entities' => ['foo', 'foo'],
      ],
    ];
    $this->assertEquals($expected, $overrides);
  }

  /**
   * Config pages loader callback returning field values.
   *
   * @param string $page
   *   Config page type.
   * @param string $field
   *   Field name.
   * @param array $deltas
   *   Field deltas.
   * @param string|null $key
   *   Field property key.
   *
   * @return string|null
   *   Field value, or NULL if not mocked.
   */
  public function getConfigPageValue($page, $field, $deltas = [], $key = NULL) {
    switch ($field) {
      case 'su_simplesaml_roles':
        return 'foo:bar,=,baz:bin';
    }
  }

}
