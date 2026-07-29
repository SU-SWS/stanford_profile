<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\layout_builder\Plugin\SectionStorage\DefaultsSectionStorage;
use Drupal\stanford_basic\Hook\PageHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Unit tests for PageHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(PageHooks::class)]
class PageHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\PageHooks
   */
  protected PageHooks $hooks;

  /**
   * Mocked theme settings provider.
   *
   * @var \Drupal\Core\Extension\ThemeSettingsProvider
   */
  protected ThemeSettingsProvider $themeSettingsProvider;

  /**
   * Mocked route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * Mocked config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->themeSettingsProvider = $this->createMock(ThemeSettingsProvider::class);
    $this->routeMatch = $this->createMock(RouteMatchInterface::class);
    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
    $this->hooks = new PageHooks(
      $this->themeSettingsProvider,
      $this->routeMatch,
      $this->configFactory,
    );

    // Default: no route parameters, so preprocessNotNodes() reaches the
    // "should center" happy path unless a test overrides this.
    $this->routeMatch->method('getParameters')
      ->willReturn(new ParameterBag([]));
  }

  /**
   * Builds a minimal $vars array with a page/content render array.
   */
  protected function buildVars(array $content = []): array {
    return [
      'page' => ['content' => $content],
    ];
  }

  /**
   * The "bright" brand bar variant is normalized to "default".
   */
  public function testPreprocessPageNormalizesBrightBrandBarVariant(): void {
    $this->themeSettingsProvider->method('getSetting')
      ->willReturnCallback(fn($name) => $name === 'brand_bar_variant' ? 'bright' : NULL);

    $vars = $this->buildVars();
    $this->hooks->preprocessPage($vars);

    $this->assertSame('su-brand-bar--default', $vars['brand_bar_variant']);
  }

  /**
   * A brand bar variant of "none" results in no brand_bar_variant key set.
   */
  public function testPreprocessPageNoneBrandBarVariantIsOmitted(): void {
    $this->themeSettingsProvider->method('getSetting')
      ->willReturnCallback(fn($name) => $name === 'brand_bar_variant' ? 'none' : NULL);

    $vars = $this->buildVars();
    $this->hooks->preprocessPage($vars);

    $this->assertArrayNotHasKey('brand_bar_variant', $vars);
  }

  /**
   * Any other brand bar variant value is used verbatim in the class name.
   */
  public function testPreprocessPageOtherBrandBarVariant(): void {
    $this->themeSettingsProvider->method('getSetting')
      ->willReturnCallback(fn($name) => $name === 'brand_bar_variant' ? 'transparent' : NULL);

    $vars = $this->buildVars();
    $this->hooks->preprocessPage($vars);

    $this->assertSame('su-brand-bar--transparent', $vars['brand_bar_variant']);
  }

  /**
   * When a global footer variant setting is present, it is added to vars.
   */
  public function testPreprocessPageSetsGlobalFooterVariantWhenPresent(): void {
    $this->themeSettingsProvider->method('getSetting')
      ->willReturnCallback(fn($name) => $name === 'global_footer_variant' ? 'dark' : NULL);

    $vars = $this->buildVars();
    $this->hooks->preprocessPage($vars);

    $this->assertSame('su-global-footer--dark', $vars['global_footer_variant']);
  }

  /**
   * When no global footer variant setting is present, nothing is added.
   */
  public function testPreprocessPageOmitsGlobalFooterVariantWhenAbsent(): void {
    $this->themeSettingsProvider->method('getSetting')->willReturn(NULL);

    $vars = $this->buildVars();
    $this->hooks->preprocessPage($vars);

    $this->assertArrayNotHasKey('global_footer_variant', $vars);
  }

  /**
   * When the "node" route parameter is present, no centered-container class
   * is added to any block, even though a block is present in content.
   */
  public function testPreprocessNotNodesReturnsEarlyForNodeRoutes(): void {
    $this->routeMatch = $this->createMock(RouteMatchInterface::class);
    $this->routeMatch->method('getParameters')
      ->willReturn(new ParameterBag(['node' => 1]));
    $this->themeSettingsProvider->method('getSetting')->willReturn(NULL);

    $this->hooks = new PageHooks(
      $this->themeSettingsProvider,
      $this->routeMatch,
      $this->configFactory,
    );

    $vars = $this->buildVars([
      'my_block' => ['#block' => TRUE],
    ]);
    $this->hooks->preprocessPage($vars);

    $this->assertArrayNotHasKey('#attributes', $vars['page']['content']['my_block']);
  }

  /**
   * When the "page_manager_page" route parameter is present, no
   * centered-container class is added.
   */
  public function testPreprocessNotNodesReturnsEarlyForPageManagerPages(): void {
    $this->routeMatch = $this->createMock(RouteMatchInterface::class);
    $this->routeMatch->method('getParameters')
      ->willReturn(new ParameterBag(['page_manager_page' => 'foo']));
    $this->themeSettingsProvider->method('getSetting')->willReturn(NULL);

    $this->hooks = new PageHooks(
      $this->themeSettingsProvider,
      $this->routeMatch,
      $this->configFactory,
    );

    $vars = $this->buildVars([
      'my_block' => ['#block' => TRUE],
    ]);
    $this->hooks->preprocessPage($vars);

    $this->assertArrayNotHasKey('#attributes', $vars['page']['content']['my_block']);
  }

  /**
   * When the layout builder UI is active (section_storage is an instance of
   * DefaultsSectionStorage), no centered-container class is added.
   */
  public function testPreprocessNotNodesReturnsEarlyForLayoutBuilderUi(): void {
    $sectionStorage = $this->createMock(DefaultsSectionStorage::class);

    $this->routeMatch = $this->createMock(RouteMatchInterface::class);
    $this->routeMatch->method('getParameters')
      ->willReturn(new ParameterBag(['section_storage' => $sectionStorage]));
    $this->themeSettingsProvider->method('getSetting')->willReturn(NULL);

    $this->hooks = new PageHooks(
      $this->themeSettingsProvider,
      $this->routeMatch,
      $this->configFactory,
    );

    $vars = $this->buildVars([
      'my_block' => ['#block' => TRUE],
    ]);
    $this->hooks->preprocessPage($vars);

    $this->assertArrayNotHasKey('#attributes', $vars['page']['content']['my_block']);
  }

  /**
   * A section_storage parameter that is NOT a DefaultsSectionStorage
   * instance does not trigger the early return, so blocks are still
   * centered.
   */
  public function testPreprocessNotNodesCentersWhenSectionStorageIsOtherType(): void {
    $sectionStorage = new \stdClass();

    $this->routeMatch = $this->createMock(RouteMatchInterface::class);
    $this->routeMatch->method('getParameters')
      ->willReturn(new ParameterBag(['section_storage' => $sectionStorage]));
    $this->themeSettingsProvider->method('getSetting')->willReturn(NULL);

    $this->hooks = new PageHooks(
      $this->themeSettingsProvider,
      $this->routeMatch,
      $this->configFactory,
    );

    $vars = $this->buildVars([
      'my_block' => ['#block' => TRUE],
    ]);
    $this->hooks->preprocessPage($vars);

    $this->assertSame(
      ['centered-container'],
      $vars['page']['content']['my_block']['#attributes']['class'],
    );
  }

  /**
   * Happy path: neither node, page_manager_page, nor layout builder section
   * storage are present, so all block items get the centered-container
   * class; non-block config (keys starting with '#') and non-block items
   * are left untouched.
   */
  public function testPreprocessNotNodesCentersBlocksOnHappyPath(): void {
    $this->themeSettingsProvider->method('getSetting')->willReturn(NULL);

    $vars = $this->buildVars([
      '#sorted' => TRUE,
      'block_one' => ['#block' => TRUE],
      'block_two' => ['#block' => TRUE],
      'not_a_block' => ['#markup' => 'hello'],
    ]);
    $this->hooks->preprocessPage($vars);

    $this->assertSame(
      ['centered-container'],
      $vars['page']['content']['block_one']['#attributes']['class'],
    );
    $this->assertSame(
      ['centered-container'],
      $vars['page']['content']['block_two']['#attributes']['class'],
    );
    $this->assertArrayNotHasKey('#attributes', $vars['page']['content']['not_a_block']);
    $this->assertSame(TRUE, $vars['page']['content']['#sorted']);
  }

  /**
   * The front-page preprocess hook sets the site name from configuration.
   */
  public function testPreprocessPageFrontSetsSiteName(): void {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->with('name')->willReturn('My Great Site');

    $this->configFactory->method('get')
      ->with('system.site')
      ->willReturn($config);

    $variables = [];
    $this->hooks->preprocessPageFront($variables);

    $this->assertSame('My Great Site', $variables['site_name']);
  }

}
