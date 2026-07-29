<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\stanford_basic\Hook\HtmlHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for HtmlHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(HtmlHooks::class)]
class HtmlHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\HtmlHooks
   */
  protected HtmlHooks $hooks;

  /**
   * Mocked path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected PathMatcherInterface $pathMatcher;

  /**
   * Mocked current path stack.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected CurrentPathStack $currentPathStack;

  /**
   * Mocked alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected AliasManagerInterface $aliasManager;

  /**
   * Mocked theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected ThemeManagerInterface $themeManager;

  /**
   * Mocked config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * Mocked module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected ModuleHandlerInterface $moduleHandler;

  /**
   * Mocked theme settings provider.
   *
   * @var \Drupal\Core\Extension\ThemeSettingsProvider
   */
  protected ThemeSettingsProvider $themeSettingsProvider;

  /**
   * The original $_ENV state, used to restore in tearDown().
   *
   * @var array
   */
  protected array $originalEnv;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->originalEnv = $_ENV;

    $this->pathMatcher = $this->createMock(PathMatcherInterface::class);
    $this->currentPathStack = $this->createMock(CurrentPathStack::class);
    $this->aliasManager = $this->createMock(AliasManagerInterface::class);
    $this->themeManager = $this->createMock(ThemeManagerInterface::class);
    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
    $this->moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $this->themeSettingsProvider = $this->createMock(ThemeSettingsProvider::class);

    $this->hooks = new HtmlHooks(
      $this->pathMatcher,
      $this->currentPathStack,
      $this->aliasManager,
      $this->themeManager,
      $this->configFactory,
      $this->moduleHandler,
      $this->themeSettingsProvider,
    );

    // Common wiring needed by every test: the active theme name lookup and
    // the theme settings config used for cacheability + variable union.
    $activeTheme = $this->createMock(ActiveTheme::class);
    $activeTheme->method('getName')->willReturn('stanford_basic');
    $this->themeManager->method('getActiveTheme')->willReturn($activeTheme);
  }

  /**
   * {@inheritDoc}
   */
  protected function tearDown(): void {
    $_ENV = $this->originalEnv;
    parent::tearDown();
  }

  /**
   * Builds a mocked ImmutableConfig for theme_name.settings.
   */
  protected function mockThemeSettingsConfig(array $original = []): ImmutableConfig {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('getOriginal')->willReturn($original);
    $config->method('getCacheContexts')->willReturn([]);
    $config->method('getCacheTags')->willReturn([]);
    $config->method('getCacheMaxAge')->willReturn(-1);
    return $config;
  }

  /**
   * Sets up the configFactory mock to return the given config for
   * '<theme>.settings' and a separate config mock for
   * 'google_analytics.settings'.
   */
  protected function mockConfigFactory(ImmutableConfig $themeSettingsConfig, ?ImmutableConfig $gaConfig = NULL): void {
    $this->configFactory->method('get')
      ->willReturnCallback(function ($name) use ($themeSettingsConfig, $gaConfig) {
        if ($name === 'stanford_basic.settings') {
          return $themeSettingsConfig;
        }
        if ($name === 'google_analytics.settings') {
          return $gaConfig;
        }
        return NULL;
      });
  }

  /**
   * No favicon setting at all results in custom_favicon being FALSE.
   */
  public function testPreprocessHtmlNoFaviconSetting(): void {
    $this->themeSettingsProvider->method('getSetting')->with('favicon')->willReturn(NULL);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertFalse($variables['custom_favicon']);
  }

  /**
   * A favicon using the default (use_default = TRUE) is not custom.
   */
  public function testPreprocessHtmlFaviconUsingDefault(): void {
    $this->themeSettingsProvider->method('getSetting')
      ->with('favicon')
      ->willReturn(['use_default' => TRUE, 'path' => '/some/favicon.ico']);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertFalse($variables['custom_favicon']);
  }

  /**
   * A favicon not using the default, but without a path, is not custom.
   */
  public function testPreprocessHtmlFaviconWithoutPath(): void {
    $this->themeSettingsProvider->method('getSetting')
      ->with('favicon')
      ->willReturn(['use_default' => FALSE, 'path' => '']);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertFalse($variables['custom_favicon']);
  }

  /**
   * A favicon not using the default, with a path, is custom.
   */
  public function testPreprocessHtmlFaviconCustom(): void {
    $this->themeSettingsProvider->method('getSetting')
      ->with('favicon')
      ->willReturn(['use_default' => FALSE, 'path' => '/some/favicon.ico']);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertTrue($variables['custom_favicon']);
  }

  /**
   * When isFrontPage() throws an exception (e.g. database unavailable),
   * is_front defaults to FALSE and the path/alias/class logic still runs.
   */
  public function testPreprocessHtmlIsFrontPageThrowsException(): void {
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willThrowException(new \Exception('no db'));
    $this->currentPathStack->method('getPath')->willReturn('/node/1');
    $this->aliasManager->method('getAliasByPath')->willReturn('/node/1');
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertFalse($variables['is_front']);
  }

  /**
   * On the front page, no page-/section- classes are added and the alias
   * manager is never consulted.
   */
  public function testPreprocessHtmlOnFrontPageSkipsClassLogic(): void {
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->aliasManager->expects($this->never())->method('getAliasByPath');
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertTrue($variables['is_front']);
    $this->assertArrayNotHasKey('class', $variables['attributes']);
  }

  /**
   * When not on the front page and the alias is empty, no classes are
   * added.
   */
  public function testPreprocessHtmlNotFrontPageWithEmptyAlias(): void {
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willReturn(FALSE);
    $this->currentPathStack->method('getPath')->willReturn('/');
    $this->aliasManager->method('getAliasByPath')->willReturn('/');
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertArrayNotHasKey('class', $variables['attributes']);
  }

  /**
   * A single-segment alias adds both a page- and matching section- class.
   */
  public function testPreprocessHtmlNotFrontPageWithSingleSegmentAlias(): void {
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willReturn(FALSE);
    $this->currentPathStack->method('getPath')->willReturn('/node/1');
    $this->aliasManager->method('getAliasByPath')->willReturn('/about');
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertContains('page-about', $variables['attributes']['class']);
    $this->assertContains('section-about', $variables['attributes']['class']);
  }

  /**
   * A multi-segment alias adds a page- class for the full alias and a
   * section- class for just the first segment.
   */
  public function testPreprocessHtmlNotFrontPageWithMultiSegmentAlias(): void {
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willReturn(FALSE);
    $this->currentPathStack->method('getPath')->willReturn('/node/1');
    $this->aliasManager->method('getAliasByPath')->willReturn('/news/2024/announcement');
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertContains('page-news-2024-announcement', $variables['attributes']['class']);
    $this->assertContains('section-news', $variables['attributes']['class']);
  }

  /**
   * Theme setting config variables are unioned into $variables, and
   * base_path() is set.
   */
  public function testPreprocessHtmlUnionsThemeSettingsAndSetsBasePath(): void {
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->mockConfigFactory($this->mockThemeSettingsConfig(['some_theme_setting' => 'value123']));

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertSame('value123', $variables['some_theme_setting']);
    $this->assertArrayHasKey('base_path', $variables);
  }

  /**
   * When AH_SITE_ENVIRONMENT is not set, no global GA variables are added.
   */
  public function testPreprocessHtmlWithoutAcquiaEnvironment(): void {
    unset($_ENV['AH_SITE_ENVIRONMENT']);
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->moduleHandler->expects($this->never())->method('moduleExists');
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertArrayNotHasKey('add_global_ga', $variables);
  }

  /**
   * When AH_SITE_ENVIRONMENT is set but the google_analytics module is not
   * enabled, add_global_ga is TRUE but ga_module_enabled is not set.
   */
  public function testPreprocessHtmlWithAcquiaEnvironmentWithoutGaModule(): void {
    $_ENV['AH_SITE_ENVIRONMENT'] = 'prod';
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->moduleHandler->method('moduleExists')->with('google_analytics')->willReturn(FALSE);
    $this->mockConfigFactory($this->mockThemeSettingsConfig());

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertTrue($variables['add_global_ga']);
    $this->assertArrayNotHasKey('ga_module_enabled', $variables);
  }

  /**
   * When AH_SITE_ENVIRONMENT is set and the google_analytics module is
   * enabled with an account configured, ga_module_enabled is TRUE.
   */
  public function testPreprocessHtmlWithAcquiaEnvironmentAndGaModuleWithAccount(): void {
    $_ENV['AH_SITE_ENVIRONMENT'] = 'prod';
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->moduleHandler->method('moduleExists')->with('google_analytics')->willReturn(TRUE);

    $gaConfig = $this->createMock(ImmutableConfig::class);
    $gaConfig->method('get')->with('account')->willReturn('UA-12345');
    $this->mockConfigFactory($this->mockThemeSettingsConfig(), $gaConfig);

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertTrue($variables['add_global_ga']);
    $this->assertTrue($variables['ga_module_enabled']);
  }

  /**
   * When the google_analytics module is enabled but no account is
   * configured, ga_module_enabled is FALSE.
   */
  public function testPreprocessHtmlWithAcquiaEnvironmentAndGaModuleWithoutAccount(): void {
    $_ENV['AH_SITE_ENVIRONMENT'] = 'prod';
    $this->themeSettingsProvider->method('getSetting')->willReturn([]);
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);
    $this->moduleHandler->method('moduleExists')->with('google_analytics')->willReturn(TRUE);

    $gaConfig = $this->createMock(ImmutableConfig::class);
    $gaConfig->method('get')->with('account')->willReturn('');
    $this->mockConfigFactory($this->mockThemeSettingsConfig(), $gaConfig);

    $variables = ['attributes' => []];
    $this->hooks->preprocessHtml($variables);

    $this->assertFalse($variables['ga_module_enabled']);
  }

}

namespace Drupal\stanford_basic\Hook;

if (!function_exists(__NAMESPACE__ . '\base_path')) {
  /**
   * Stub for base_path() so unit tests don't require a full bootstrap.
   */
  function base_path() {
    return '/';
  }
}
