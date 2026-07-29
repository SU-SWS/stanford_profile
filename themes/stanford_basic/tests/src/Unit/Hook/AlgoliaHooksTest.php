<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\stanford_basic\Hook\AlgoliaHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for AlgoliaHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(AlgoliaHooks::class)]
class AlgoliaHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\AlgoliaHooks
   */
  protected AlgoliaHooks $hooks;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->hooks = new AlgoliaHooks();
    $this->hooks->setStringTranslation($this->getStringTranslationStub());
    $this->resetAlgoliaSettingsCache();
  }

  /**
   * {@inheritDoc}
   */
  protected function tearDown(): void {
    $this->resetAlgoliaSettingsCache();
    parent::tearDown();
  }

  /**
   * Resets the internal static cache property between tests so that one
   * test's cached result cannot leak into another.
   */
  protected function resetAlgoliaSettingsCache(): void {
    $property = new \ReflectionProperty(AlgoliaHooks::class, 'algoliaSettings');
    $property->setAccessible(TRUE);
    $property->setValue(NULL, NULL);
  }

  /**
   * When the config_pages.loader service is unavailable, settings are FALSE.
   */
  public function testGetAlgoliaSettingsReturnsFalseWhenServiceUnavailable(): void {
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);

    $this->assertFalse(AlgoliaHooks::getAlgoliaSettings());
  }

  /**
   * When algolia is not enabled in the config page, settings are FALSE.
   */
  public function testGetAlgoliaSettingsReturnsFalseWhenAlgoliaDisabled(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->expects($this->once())
      ->method('getValue')
      ->with('stanford_basic_site_settings', 'su_site_algolia_ui', 0, 'value')
      ->willReturn(0);

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $this->assertFalse(AlgoliaHooks::getAlgoliaSettings());
  }

  /**
   * Happy path: algolia is enabled and settings are returned.
   */
  public function testGetAlgoliaSettingsHappyPath(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->method('getValue')
      ->willReturnCallback(function ($type, $field, $deltas = [], $key = NULL) {
        return match ($field) {
          'su_site_algolia_ui' => 1,
          'su_site_algolia_id' => 'app123',
          'su_site_algolia_search' => 'searchkey123',
          'su_site_algolia_index' => 'my_index',
          'su_site_algolia_fed' => 1,
          default => NULL,
        };
      });

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $expected = [
      'appId' => 'app123',
      'searchKey' => 'searchkey123',
      'index' => 'my_index',
      'federatedSearch' => TRUE,
    ];
    $this->assertSame($expected, AlgoliaHooks::getAlgoliaSettings());
  }

  /**
   * The result is cached statically; a second call does not re-query the
   * config_pages.loader service.
   */
  public function testGetAlgoliaSettingsCachesResult(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->expects($this->once())
      ->method('getValue')
      ->with('stanford_basic_site_settings', 'su_site_algolia_ui', 0, 'value')
      ->willReturn(0);

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $this->assertFalse(AlgoliaHooks::getAlgoliaSettings());
    // Second call should hit the static cache, not the mock again. Since the
    // mock expects `getValue` exactly once above, calling it again here would
    // fail the test if the cache were not used.
    $this->assertFalse(AlgoliaHooks::getAlgoliaSettings());
  }

  /**
   * When algolia settings are not configured, preprocessPageSearch() returns
   * early and does not attach any library or drupalSettings.
   */
  public function testPreprocessPageSearchReturnsEarlyWhenNotConfigured(): void {
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);

    $variables = ['page' => ['content' => []]];
    $this->hooks->preprocessPageSearch($variables);

    $this->assertArrayNotHasKey('#attached', $variables);
  }

  /**
   * When algolia settings are configured, the library and drupalSettings are
   * attached, and the system_main_block content is replaced with a
   * noscript-friendly container.
   */
  public function testPreprocessPageSearchAttachesLibraryAndReplacesMainBlock(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->method('getValue')
      ->willReturnCallback(function ($type, $field, $deltas = [], $key = NULL) {
        return match ($field) {
          'su_site_algolia_ui' => 1,
          'su_site_algolia_id' => 'app123',
          'su_site_algolia_search' => 'searchkey123',
          'su_site_algolia_index' => 'my_index',
          'su_site_algolia_fed' => 0,
          default => NULL,
        };
      });

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $variables = [
      'page' => [
        'content' => [
          'some_block' => ['#plugin_id' => 'system_main_block'],
          'other_block' => ['#plugin_id' => 'some_other_block'],
        ],
      ],
    ];
    $this->hooks->preprocessPageSearch($variables);

    $this->assertSame('stanford_basic/algolia-search', $variables['#attached']['library'][0]);
    $this->assertSame([
      'appId' => 'app123',
      'searchKey' => 'searchkey123',
      'index' => 'my_index',
      'federatedSearch' => FALSE,
    ], $variables['#attached']['drupalSettings']['stanfordAlgolia']);

    $this->assertSame('container', $variables['page']['content']['some_block']['#type']);
    $this->assertSame('algolia-search', $variables['page']['content']['some_block']['#attributes']['id']);
    $this->assertSame(['centered-container'], $variables['page']['content']['some_block']['#attributes']['class']);
    $this->assertSame('html_tag', $variables['page']['content']['some_block']['noscript']['#type']);
    $this->assertSame('noscript', $variables['page']['content']['some_block']['noscript']['#tag']);

    // The other block, which is not system_main_block, is left untouched.
    $this->assertSame(['#plugin_id' => 'some_other_block'], $variables['page']['content']['other_block']);
  }

}
