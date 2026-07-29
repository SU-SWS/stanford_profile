<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Menu\MenuLinkInterface;
use Drupal\Core\Menu\MenuLinkTreeElement;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Url;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\stanford_basic\Hook\AlgoliaHooks;
use Drupal\stanford_basic\Hook\BlockHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for BlockHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(BlockHooks::class)]
class BlockHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\BlockHooks
   */
  protected BlockHooks $hooks;

  /**
   * Mocked entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Mocked menu link tree.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected MenuLinkTreeInterface $menuTree;

  /**
   * Mocked theme settings provider.
   *
   * @var \Drupal\Core\Extension\ThemeSettingsProvider
   */
  protected ThemeSettingsProvider $themeSettingsProvider;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->menuTree = $this->createMock(MenuLinkTreeInterface::class);
    $this->themeSettingsProvider = $this->createMock(ThemeSettingsProvider::class);
    $this->hooks = new BlockHooks($this->entityTypeManager, $this->menuTree, $this->themeSettingsProvider);
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
   * Resets AlgoliaHooks' internal static cache property so results from one
   * test do not leak into another, since BlockHooks::preprocessBlock()
   * indirectly calls AlgoliaHooks::getAlgoliaSettings().
   */
  protected function resetAlgoliaSettingsCache(): void {
    $property = new \ReflectionProperty(AlgoliaHooks::class, 'algoliaSettings');
    $property->setAccessible(TRUE);
    $property->setValue(NULL, NULL);
  }

  /**
   * Base variables array shared across preprocessBlock() tests.
   */
  protected function baseVariables(string $plugin_id = 'some_block'): array {
    return [
      'plugin_id' => $plugin_id,
      'base_plugin_id' => 'some base:plugin',
      'derivative_plugin_id' => 'some derivative!plugin',
      'attributes' => [],
      'title_attributes' => [],
      'configuration' => [],
      'content' => ['#markup' => 'hello'],
    ];
  }

  /**
   * The base and derivative plugin id css classes are always added,
   * regardless of the specific plugin.
   */
  public function testPreprocessBlockAddsCssClassesForPluginIds(): void {
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);

    $variables = $this->baseVariables();
    $this->hooks->preprocessBlock($variables);

    $this->assertContains('some-base-plugin', $variables['attributes']['class']);
    $this->assertContains('some-derivative-plugin', $variables['attributes']['class']);
  }

  /**
   * When the plugin is not the main menu block, the config_pages.loader
   * service is never used, even if it exists.
   */
  public function testPreprocessBlockNonMainMenuBlockSkipsMenuIslandLogic(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->expects($this->never())->method('getValue');

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $variables = $this->baseVariables('system_menu_block:footer');
    $this->hooks->preprocessBlock($variables);

    $this->assertArrayNotHasKey('data-island', $variables['attributes']);
  }

  /**
   * When the plugin is the main menu block but config_pages.loader is not
   * available, the menu island logic is skipped entirely.
   */
  public function testPreprocessBlockMainMenuBlockWithoutConfigPagesService(): void {
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);

    $this->menuTree->expects($this->never())->method('load');

    $variables = $this->baseVariables('system_menu_block:main');
    $this->hooks->preprocessBlock($variables);

    $this->assertArrayNotHasKey('data-island', $variables['attributes']);
    $this->assertArrayNotHasKey('#attached', $variables);
  }

  /**
   * When the main menu block is present, config_pages.loader is available,
   * but the new menu setting is off, the menu island logic is skipped.
   */
  public function testPreprocessBlockMainMenuBlockWithNewMenuDisabled(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->method('getValue')
      ->with('stanford_basic_site_settings', 'su_site_new_menu', 0, 'value')
      ->willReturn(0);

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $this->menuTree->expects($this->never())->method('load');

    $variables = $this->baseVariables('system_menu_block:main');
    $this->hooks->preprocessBlock($variables);

    $this->assertArrayNotHasKey('data-island', $variables['attributes']);
  }

  /**
   * Builds a mocked MenuLinkTreeElement with a mocked link.
   */
  protected function buildMenuLinkTreeElement(
    string $title,
    int $weight,
    bool $enabled = TRUE,
    bool $accessible = TRUE,
    array $subtree = [],
  ): MenuLinkTreeElement {
    $url = $this->createMock(Url::class);
    $url->method('access')->willReturn($accessible);
    $url->method('toString')->willReturn('/' . strtolower(str_replace(' ', '-', $title)));

    $link = $this->createMock(MenuLinkInterface::class);
    $link->method('isEnabled')->willReturn($enabled);
    $link->method('getUrlObject')->willReturn($url);
    $link->method('getTitle')->willReturn($title);
    $link->method('isExpanded')->willReturn(FALSE);
    $link->method('getParent')->willReturn('');
    $link->method('getWeight')->willReturn($weight);

    return new MenuLinkTreeElement($link, !empty($subtree), 0, FALSE, $subtree);
  }

  /**
   * When the main menu block is present, config_pages.loader is available,
   * and the new menu setting is on, the decoupled menu island attachments
   * and recursively-built menu tree links are added.
   */
  public function testPreprocessBlockMainMenuBlockWithNewMenuEnabled(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->method('getValue')
      ->with('stanford_basic_site_settings', 'su_site_new_menu', 0, 'value')
      ->willReturn(1);

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $child = $this->buildMenuLinkTreeElement('Child Link', 5);
    $disabledChild = $this->buildMenuLinkTreeElement('Disabled Link', 1, enabled: FALSE);
    $inaccessibleChild = $this->buildMenuLinkTreeElement('No Access Link', 1, accessible: FALSE);
    $parentB = $this->buildMenuLinkTreeElement('Bravo', 10, subtree: [$child, $disabledChild, $inaccessibleChild]);
    $parentA = $this->buildMenuLinkTreeElement('Alpha', 10);

    $tree = ['bravo' => $parentB, 'alpha' => $parentA];

    $this->menuTree->expects($this->once())
      ->method('load')
      ->willReturn($tree);

    $variables = $this->baseVariables('system_menu_block:main');
    $this->hooks->preprocessBlock($variables);

    $this->assertSame('main-menu-island', $variables['attributes']['data-island']);
    $this->assertContains('stanford_basic/decoupled_menu', $variables['#attached']['library']);
    $this->assertContains('stanford_profile_helper:menu_links', $variables['elements']['#cache']['tags']);

    $links = $variables['#attached']['drupalSettings']['stanford_basic']['decoupledMenuItems'];
    $this->assertCount(2, $links);
    // Same weight (10) for both top level items, so sorted by title: Alpha
    // before Bravo.
    $this->assertSame('Alpha', $links[0]['title']);
    $this->assertSame('Bravo', $links[1]['title']);

    // Only the enabled + accessible child should appear in the subtree.
    $this->assertCount(1, $links[1]['items']);
    $this->assertSame('Child Link', $links[1]['items'][0]['title']);
  }

  /**
   * On the algolia search results block, if algolia settings are configured
   * (truthy), the content is removed.
   */
  public function testPreprocessBlockAlgoliaSearchResultsWithSettingsConfigured(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->method('getValue')
      ->willReturnCallback(function ($type, $field, $deltas = [], $key = NULL) {
        return match ($field) {
          'su_site_algolia_ui' => 1,
          'su_site_algolia_id' => 'app123',
          'su_site_algolia_search' => 'searchkey',
          'su_site_algolia_index' => 'index',
          'su_site_algolia_fed' => 0,
          default => NULL,
        };
      });

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $variables = $this->baseVariables('views_exposed_filter_block:search-results');
    $this->hooks->preprocessBlock($variables);

    $this->assertArrayNotHasKey('content', $variables);
  }

  /**
   * On the algolia search results block, if algolia settings are not
   * configured (falsy), the content is left intact.
   */
  public function testPreprocessBlockAlgoliaSearchResultsWithoutSettingsConfigured(): void {
    $configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $configPages->method('getValue')
      ->with('stanford_basic_site_settings', 'su_site_algolia_ui', 0, 'value')
      ->willReturn(0);

    $container = new ContainerBuilder();
    $container->set('config_pages.loader', $configPages);
    \Drupal::setContainer($container);

    $variables = $this->baseVariables('views_exposed_filter_block:search-results');
    $this->hooks->preprocessBlock($variables);

    $this->assertArrayHasKey('content', $variables);
    $this->assertSame(['#markup' => 'hello'], $variables['content']);
  }

  /**
   * When label_display is not set at all, aria-hidden is set to true.
   */
  public function testPreprocessBlockAriaHiddenWhenLabelDisplayNotSet(): void {
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);

    $variables = $this->baseVariables();
    unset($variables['configuration']['label_display']);
    $this->hooks->preprocessBlock($variables);

    $this->assertSame('true', $variables['title_attributes']['aria-hidden']);
  }

  /**
   * When label_display is set but not to "visible", aria-hidden is true.
   */
  public function testPreprocessBlockAriaHiddenWhenLabelDisplayNotVisible(): void {
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);

    $variables = $this->baseVariables();
    $variables['configuration']['label_display'] = 0;
    $this->hooks->preprocessBlock($variables);

    $this->assertSame('true', $variables['title_attributes']['aria-hidden']);
  }

  /**
   * When label_display is set to "visible", aria-hidden is not added.
   */
  public function testPreprocessBlockNoAriaHiddenWhenLabelDisplayVisible(): void {
    $container = new ContainerBuilder();
    \Drupal::setContainer($container);

    $variables = $this->baseVariables();
    $variables['configuration']['label_display'] = BlockPluginInterface::BLOCK_LABEL_VISIBLE;
    $this->hooks->preprocessBlock($variables);

    $this->assertArrayNotHasKey('aria-hidden', $variables['title_attributes']);
  }

  /**
   * When the base plugin id is not system_menu_block, nothing happens.
   */
  public function testThemeSuggestionsBlockAlterIgnoresNonMenuBlocks(): void {
    $this->entityTypeManager->expects($this->never())->method('hasDefinition');

    $suggestions = [];
    $variables = ['elements' => ['#base_plugin_id' => 'some_other_block']];
    $this->hooks->themeSuggestionsBlockAlter($suggestions, $variables);

    $this->assertSame([], $suggestions);
  }

  /**
   * When the taxonomy_menu entity type does not exist, nothing happens.
   */
  public function testThemeSuggestionsBlockAlterWithoutTaxonomyMenuEntityType(): void {
    $this->entityTypeManager->method('hasDefinition')->with('taxonomy_menu')->willReturn(FALSE);
    $this->entityTypeManager->expects($this->never())->method('getStorage');

    $suggestions = [];
    $variables = [
      'elements' => [
        '#base_plugin_id' => 'system_menu_block',
        '#derivative_plugin_id' => 'main',
      ],
    ];
    $this->hooks->themeSuggestionsBlockAlter($suggestions, $variables);

    $this->assertSame([], $suggestions);
  }

  /**
   * When the taxonomy_menu entity type exists but no taxonomy menu matches
   * the derivative plugin id (menu name), nothing happens.
   */
  public function testThemeSuggestionsBlockAlterWithNoMatchingTaxonomyMenu(): void {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadByProperties')->with(['menu' => 'main'])->willReturn([]);

    $this->entityTypeManager->method('hasDefinition')->with('taxonomy_menu')->willReturn(TRUE);
    $this->entityTypeManager->method('getStorage')->with('taxonomy_menu')->willReturn($storage);

    $suggestions = [];
    $variables = [
      'elements' => [
        '#base_plugin_id' => 'system_menu_block',
        '#derivative_plugin_id' => 'main',
      ],
    ];
    $this->hooks->themeSuggestionsBlockAlter($suggestions, $variables);

    $this->assertSame([], $suggestions);
  }

  /**
   * When a taxonomy menu matches, the suggestion is added.
   */
  public function testThemeSuggestionsBlockAlterWithMatchingTaxonomyMenu(): void {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadByProperties')->with(['menu' => 'tags'])->willReturn(['some_taxonomy_menu']);

    $this->entityTypeManager->method('hasDefinition')->with('taxonomy_menu')->willReturn(TRUE);
    $this->entityTypeManager->method('getStorage')->with('taxonomy_menu')->willReturn($storage);

    $suggestions = [];
    $variables = [
      'elements' => [
        '#base_plugin_id' => 'system_menu_block',
        '#derivative_plugin_id' => 'tags',
      ],
    ];
    $this->hooks->themeSuggestionsBlockAlter($suggestions, $variables);

    $this->assertSame(['block__system_menu_block__filter_by'], $suggestions);
  }

  /**
   * The branding block preprocess hook passes through the logo and lockup
   * theme settings.
   */
  public function testPreprocessBlockSystemBrandingBlockWithLockupSet(): void {
    $this->themeSettingsProvider->method('getSetting')
      ->willReturnCallback(function ($name) {
        return match ($name) {
          'logo.use_default' => FALSE,
          'lockup' => ['option' => 'b'],
          default => NULL,
        };
      });

    $vars = [];
    $this->hooks->preprocessBlockSystemBrandingBlock($vars);

    $this->assertFalse($vars['use_logo']);
    $this->assertSame(['option' => 'b'], $vars['lockup']);
  }

  /**
   * When no lockup setting exists, a default is used.
   */
  public function testPreprocessBlockSystemBrandingBlockWithoutLockupSet(): void {
    $this->themeSettingsProvider->method('getSetting')
      ->willReturnCallback(function ($name) {
        return match ($name) {
          'logo.use_default' => TRUE,
          'lockup' => NULL,
          default => NULL,
        };
      });

    $vars = [];
    $this->hooks->preprocessBlockSystemBrandingBlock($vars);

    $this->assertTrue($vars['use_logo']);
    $this->assertSame(['option' => 'a'], $vars['lockup']);
  }

  /**
   * changeCharacters() replaces non-alphanumeric/whitespace characters with a
   * dash, and returns an empty string for empty input.
   */
  public function testChangeCharacters(): void {
    $method = new \ReflectionMethod(BlockHooks::class, 'changeCharacters');
    $method->setAccessible(TRUE);

    $this->assertSame('foo-bar', $method->invoke($this->hooks, 'foo:bar'));
    $this->assertSame('', $method->invoke($this->hooks, ''));
  }

  /**
   * getMenuTreeLinks() returns an empty array for an empty tree.
   */
  public function testGetMenuTreeLinksWithEmptyTree(): void {
    $method = new \ReflectionMethod(BlockHooks::class, 'getMenuTreeLinks');
    $method->setAccessible(TRUE);

    $this->assertSame([], $method->invoke($this->hooks, []));
  }

}
