<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\GeneratedUrl;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\stanford_basic\Hook\MenuHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Unit tests for MenuHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(MenuHooks::class)]
class MenuHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\MenuHooks
   */
  protected MenuHooks $hooks;

  /**
   * Mocked request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected RequestStack $requestStack;

  /**
   * Mocked theme settings provider.
   *
   * @var \Drupal\Core\Extension\ThemeSettingsProvider
   */
  protected ThemeSettingsProvider $themeSettingsProvider;

  /**
   * Mocked path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcherInterface
   */
  protected PathMatcherInterface $pathMatcher;

  /**
   * Mocked config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * Mocked alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected AliasManagerInterface $aliasManager;

  /**
   * Mocked entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The base path returned by the namespaced base_path() stub below.
   *
   * @var string
   */
  const TEST_BASE_PATH = '/';

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->requestStack = $this->createMock(RequestStack::class);
    $this->themeSettingsProvider = $this->createMock(ThemeSettingsProvider::class);
    $this->pathMatcher = $this->createMock(PathMatcherInterface::class);
    $this->configFactory = $this->createMock(ConfigFactoryInterface::class);
    $this->aliasManager = $this->createMock(AliasManagerInterface::class);
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);

    $this->hooks = new MenuHooks(
      $this->requestStack,
      $this->themeSettingsProvider,
      $this->pathMatcher,
      $this->configFactory,
      $this->aliasManager,
      $this->entityTypeManager,
    );
    $this->hooks->setStringTranslation($this->getStringTranslationStub());
  }

  /**
   * Configures the configFactory/aliasManager mocks so that any call to the
   * protected pathIsHome() method (e.g. indirectly via menuProcessSubmenu())
   * resolves without error. Uses lenient method() stubs (no call-count
   * assertions) since several tests trigger this as a side effect of
   * evaluating the left-hand side of a boolean OR.
   */
  protected function stubFrontPageConfig(string $frontUri = '/node/1', string $frontAlias = '/home-alias'): void {
    $siteConfig = $this->createMock(ImmutableConfig::class);
    $siteConfig->method('get')->with('page.front')->willReturn($frontUri);
    $this->configFactory->method('get')->with('system.site')->willReturn($siteConfig);
    $this->aliasManager->method('getAliasByPath')->with($frontUri)->willReturn($frontAlias);
  }

  /**
   * Sets the current request's URI that preprocessMenu() reads.
   */
  protected function setCurrentPath(string $path): void {
    $request = $this->createMock(Request::class);
    $request->method('getRequestUri')->willReturn($path);
    $this->requestStack->method('getCurrentRequest')->willReturn($request);
  }

  /**
   * Builds a mock Url whose toString(TRUE)->getGeneratedUrl() returns the
   * given path, and whose getOptions()/setOptions() can be inspected.
   */
  protected function mockUrl(string $generatedPath, array $options = []): Url {
    $generatedUrl = new GeneratedUrl();
    $generatedUrl->setGeneratedUrl($generatedPath);

    $url = $this->createMock(Url::class);
    $url->method('toString')->with(TRUE)->willReturn($generatedUrl);
    $url->method('getOptions')->willReturn($options);
    return $url;
  }

  /**
   * Invokes the protected menuProcessSubmenu() method via reflection.
   */
  protected function callMenuProcessSubmenu(array &$submenu, string $currentPath): void {
    $method = new \ReflectionMethod(MenuHooks::class, 'menuProcessSubmenu');
    $method->setAccessible(TRUE);
    $method->invokeArgs($this->hooks, [&$submenu, $currentPath]);
  }

  /**
   * Invokes the protected pathIsHome() method via reflection.
   */
  protected function callPathIsHome(string $path): bool {
    $method = new \ReflectionMethod(MenuHooks::class, 'pathIsHome');
    $method->setAccessible(TRUE);
    return $method->invoke($this->hooks, $path);
  }

  /**
   * Invokes the protected linkIsPublic() method via reflection.
   */
  protected function callLinkIsPublic(Url $url): bool {
    $method = new \ReflectionMethod(MenuHooks::class, 'linkIsPublic');
    $method->setAccessible(TRUE);
    return $method->invoke($this->hooks, $url);
  }

  /**
   * When a menu item's URL matches the current path, aria-current is added
   * to both the URL options and the item's own attributes.
   */
  public function testPreprocessMenuAddsAriaCurrentWhenPathMatches(): void {
    $this->setCurrentPath('/current/path');

    $url = $this->mockUrl('/current/path');
    $url->expects($this->once())
      ->method('setOptions')
      ->with(['attributes' => ['aria-current' => 'true']]);

    $variables = [
      'menu_name' => 'footer',
      'items' => [
        'home' => ['url' => $url],
      ],
    ];
    $this->hooks->preprocessMenu($variables, 'menu__footer');

    $this->assertSame('true', $variables['items']['home']['attributes']['aria-current']);
  }

  /**
   * When a menu item's URL does not match the current path, no aria-current
   * attribute is added anywhere.
   */
  public function testPreprocessMenuNoAriaCurrentWhenPathDoesNotMatch(): void {
    $this->setCurrentPath('/current/path');

    $url = $this->mockUrl('/other/path');
    $url->expects($this->never())->method('setOptions');

    $variables = [
      'menu_name' => 'footer',
      'items' => [
        'other' => ['url' => $url],
      ],
    ];
    $this->hooks->preprocessMenu($variables, 'menu__footer');

    $this->assertArrayNotHasKey('attributes', $variables['items']['other']);
  }

  /**
   * Non-main menus never run the submenu processing logic or check the
   * nav_dropdown_enabled theme setting.
   */
  public function testPreprocessMenuReturnsEarlyForNonMainMenu(): void {
    $this->setCurrentPath('/current/path');
    $this->pathMatcher->expects($this->never())->method('isFrontPage');
    $this->themeSettingsProvider->expects($this->never())->method('getSetting');

    $variables = [
      'menu_name' => 'footer',
      'items' => [],
    ];
    $this->hooks->preprocessMenu($variables, 'menu__footer');

    $this->assertArrayNotHasKey('attributes', $variables);
  }

  /**
   * On the main menu, when the dropdown theme setting is disabled, no extra
   * class is added.
   */
  public function testPreprocessMenuMainMenuWithoutDropdownSetting(): void {
    $this->setCurrentPath('/current/path');
    $this->stubFrontPageConfig();
    $this->pathMatcher->method('isFrontPage')->willReturn(FALSE);
    $this->themeSettingsProvider->method('getSetting')
      ->with('nav_dropdown_enabled', 'stanford_basic')
      ->willReturn(FALSE);

    $url = $this->mockUrl('/other/path');

    $variables = [
      'menu_name' => 'main',
      'items' => [
        'other' => ['url' => $url, 'below' => []],
      ],
    ];
    $this->hooks->preprocessMenu($variables, 'menu__main');

    $this->assertArrayNotHasKey('attributes', $variables);
  }

  /**
   * On the main menu, when the dropdown theme setting is enabled, the
   * su-multi-menu--dropdowns class is added, and matching submenu items are
   * marked active via the recursive menuProcessSubmenu() call.
   */
  public function testPreprocessMenuMainMenuAddsDropdownClassAndMarksActiveItems(): void {
    $this->setCurrentPath('/current/path');
    $this->stubFrontPageConfig();
    $this->pathMatcher->method('isFrontPage')->willReturn(FALSE);
    $this->themeSettingsProvider->method('getSetting')
      ->with('nav_dropdown_enabled', 'stanford_basic')
      ->willReturn(TRUE);

    $activeUrl = $this->mockUrl('/current/path');
    $otherUrl = $this->mockUrl('/other/path');

    $variables = [
      'menu_name' => 'main',
      'items' => [
        'active' => ['url' => $activeUrl, 'below' => []],
        'other' => ['url' => $otherUrl, 'below' => []],
      ],
    ];
    $this->hooks->preprocessMenu($variables, 'menu__main');

    $this->assertContains('su-multi-menu--dropdowns', $variables['attributes']['class']);
    $this->assertTrue($variables['items']['active']['is_active']);
    $this->assertArrayNotHasKey('is_active', $variables['items']['other']);
  }

  /**
   * String URLs (unrouted paths) are compared directly against the current
   * path, without calling toString().
   */
  public function testMenuProcessSubmenuHandlesStringUrls(): void {
    $this->pathMatcher->method('isFrontPage')->willReturn(FALSE);

    $submenu = [
      'string_item' => ['url' => '/current/path', 'below' => []],
    ];
    $this->callMenuProcessSubmenu($submenu, '/current/path');

    $this->assertTrue($submenu['string_item']['is_active']);
    // Url instanceof check must be FALSE for strings, so 'unpublished' is
    // never set.
    $this->assertArrayNotHasKey('unpublished', $submenu['string_item']);
  }

  /**
   * When the front page matcher is TRUE and the item's path corresponds to
   * the configured front page, the item is marked active even though its
   * own path differs from the literal current path.
   */
  public function testMenuProcessSubmenuMarksFrontPageItemActive(): void {
    $this->pathMatcher->method('isFrontPage')->willReturn(TRUE);

    $siteConfig = $this->createMock(ImmutableConfig::class);
    $siteConfig->method('get')->with('page.front')->willReturn('/node/1');
    $this->configFactory->method('get')->with('system.site')->willReturn($siteConfig);
    $this->aliasManager->method('getAliasByPath')->with('/node/1')->willReturn('/home');

    $submenu = [
      'home' => ['url' => '/home', 'below' => []],
    ];
    $this->callMenuProcessSubmenu($submenu, '/some/other/current/path');

    $this->assertTrue($submenu['home']['is_active']);
  }

  /**
   * Recursion into 'below' items is exercised, and an unpublished node link
   * deep in the tree is flagged.
   */
  public function testMenuProcessSubmenuRecursesIntoBelowItemsAndFlagsUnpublished(): void {
    $this->stubFrontPageConfig();
    $this->pathMatcher->method('isFrontPage')->willReturn(FALSE);

    $childUrl = $this->createMock(Url::class);
    $childUrl->method('toString')->with(TRUE)->willReturn((new GeneratedUrl())->setGeneratedUrl('/child'));
    $childUrl->method('isRouted')->willReturn(TRUE);
    $childUrl->method('getRouteName')->willReturn('entity.node.canonical');
    $childUrl->method('getRouteParameters')->willReturn(['node' => 5]);

    $node = $this->createMock(NodeInterface::class);
    $node->method('isPublished')->willReturn(FALSE);
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with(5)->willReturn($node);
    $this->entityTypeManager->method('getStorage')->with('node')->willReturn($storage);

    $parentUrl = $this->mockUrl('/parent');

    $submenu = [
      'parent' => [
        'url' => $parentUrl,
        'below' => [
          'child' => ['url' => $childUrl, 'below' => []],
        ],
      ],
    ];
    $this->callMenuProcessSubmenu($submenu, '/current/path');

    $this->assertTrue($submenu['parent']['below']['child']['unpublished']);
  }

  /**
   * A published node link is not flagged as unpublished.
   */
  public function testLinkIsPublicTrueForPublishedNode(): void {
    $url = $this->createMock(Url::class);
    $url->method('isRouted')->willReturn(TRUE);
    $url->method('getRouteName')->willReturn('entity.node.canonical');
    $url->method('getRouteParameters')->willReturn(['node' => 1]);

    $node = $this->createMock(NodeInterface::class);
    $node->method('isPublished')->willReturn(TRUE);
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with(1)->willReturn($node);
    $this->entityTypeManager->method('getStorage')->with('node')->willReturn($storage);

    $this->assertTrue($this->callLinkIsPublic($url));
  }

  /**
   * An unpublished node link is correctly identified as not public.
   */
  public function testLinkIsPublicFalseForUnpublishedNode(): void {
    $url = $this->createMock(Url::class);
    $url->method('isRouted')->willReturn(TRUE);
    $url->method('getRouteName')->willReturn('entity.node.canonical');
    $url->method('getRouteParameters')->willReturn(['node' => 2]);

    $node = $this->createMock(NodeInterface::class);
    $node->method('isPublished')->willReturn(FALSE);
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with(2)->willReturn($node);
    $this->entityTypeManager->method('getStorage')->with('node')->willReturn($storage);

    $this->assertFalse($this->callLinkIsPublic($url));
  }

  /**
   * A node canonical route whose node no longer exists is treated as
   * public (TRUE), since there is nothing to hide.
   */
  public function testLinkIsPublicTrueWhenNodeMissing(): void {
    $url = $this->createMock(Url::class);
    $url->method('isRouted')->willReturn(TRUE);
    $url->method('getRouteName')->willReturn('entity.node.canonical');
    $url->method('getRouteParameters')->willReturn(['node' => 999]);

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with(999)->willReturn(NULL);
    $this->entityTypeManager->method('getStorage')->with('node')->willReturn($storage);

    $this->assertTrue($this->callLinkIsPublic($url));
  }

  /**
   * A routed URL for a non-node route is always public, and the entity type
   * manager is never queried.
   */
  public function testLinkIsPublicTrueForNonNodeRoute(): void {
    $this->entityTypeManager->expects($this->never())->method('getStorage');

    $url = $this->createMock(Url::class);
    $url->method('isRouted')->willReturn(TRUE);
    $url->method('getRouteName')->willReturn('entity.taxonomy_term.canonical');

    $this->assertTrue($this->callLinkIsPublic($url));
  }

  /**
   * A non-routed (external/unrouted) URL is always public.
   */
  public function testLinkIsPublicTrueForNonRoutedUrl(): void {
    $this->entityTypeManager->expects($this->never())->method('getStorage');

    $url = $this->createMock(Url::class);
    $url->method('isRouted')->willReturn(FALSE);
    $url->expects($this->never())->method('getRouteName');

    $this->assertTrue($this->callLinkIsPublic($url));
  }

  /**
   * pathIsHome() returns TRUE when the given path equals the site's base
   * path, and the front-page config lookup is only performed once (cached
   * on the instance for subsequent calls).
   */
  public function testPathIsHomeMatchesBasePathAndCachesLookup(): void {
    $siteConfig = $this->createMock(ImmutableConfig::class);
    $siteConfig->method('get')->with('page.front')->willReturn('/node/1');
    $this->configFactory->expects($this->once())
      ->method('get')
      ->with('system.site')
      ->willReturn($siteConfig);
    $this->aliasManager->expects($this->once())
      ->method('getAliasByPath')
      ->with('/node/1')
      ->willReturn('/home-alias');

    $this->assertTrue($this->callPathIsHome(self::TEST_BASE_PATH));
    // Second call must hit the cached $frontPath property rather than
    // re-querying config/alias services (enforced by expects($this->once())
    // above).
    $this->assertTrue($this->callPathIsHome(self::TEST_BASE_PATH));
  }

  /**
   * pathIsHome() returns TRUE when the path matches the configured front
   * page's internal URI.
   */
  public function testPathIsHomeMatchesFrontUri(): void {
    $siteConfig = $this->createMock(ImmutableConfig::class);
    $siteConfig->method('get')->with('page.front')->willReturn('/node/1');
    $this->configFactory->method('get')->with('system.site')->willReturn($siteConfig);
    $this->aliasManager->method('getAliasByPath')->with('/node/1')->willReturn('/home-alias');

    $this->assertTrue($this->callPathIsHome('/node/1'));
  }

  /**
   * pathIsHome() returns TRUE when the path matches the front page's alias.
   */
  public function testPathIsHomeMatchesFrontAlias(): void {
    $siteConfig = $this->createMock(ImmutableConfig::class);
    $siteConfig->method('get')->with('page.front')->willReturn('/node/1');
    $this->configFactory->method('get')->with('system.site')->willReturn($siteConfig);
    $this->aliasManager->method('getAliasByPath')->with('/node/1')->willReturn('/home-alias');

    $this->assertTrue($this->callPathIsHome('/home-alias'));
  }

  /**
   * pathIsHome() returns TRUE for the literal '<front>' placeholder path.
   */
  public function testPathIsHomeMatchesFrontPlaceholder(): void {
    $siteConfig = $this->createMock(ImmutableConfig::class);
    $siteConfig->method('get')->with('page.front')->willReturn('/node/1');
    $this->configFactory->method('get')->with('system.site')->willReturn($siteConfig);
    $this->aliasManager->method('getAliasByPath')->with('/node/1')->willReturn('/home-alias');

    $this->assertTrue($this->callPathIsHome('<FRONT>'));
  }

  /**
   * pathIsHome() returns FALSE for a path unrelated to the front page.
   */
  public function testPathIsHomeReturnsFalseForUnrelatedPath(): void {
    $siteConfig = $this->createMock(ImmutableConfig::class);
    $siteConfig->method('get')->with('page.front')->willReturn('/node/1');
    $this->configFactory->method('get')->with('system.site')->willReturn($siteConfig);
    $this->aliasManager->method('getAliasByPath')->with('/node/1')->willReturn('/home-alias');

    $this->assertFalse($this->callPathIsHome('/totally/unrelated'));
  }

  /**
   * When the linked route is the node version history route, the title is
   * changed to "Version History" and the class is derived from that new
   * title.
   */
  public function testPreprocessMenuLocalTaskVersionHistory(): void {
    $url = $this->createMock(Url::class);
    $url->method('getRouteName')->willReturn('entity.node.version_history');

    $variables = [
      'link' => ['#url' => $url, '#title' => 'Revisions'],
      'attributes' => [],
    ];
    $this->hooks->preprocessMenuLocalTask($variables);

    $this->assertSame('Version History', (string) $variables['link']['#title']);
    $this->assertContains('version-history', $variables['attributes']['class']);
  }

  /**
   * When the linked route is the node canonical route, the title is
   * changed to "Page Content" and the page-content-label class is added.
   */
  public function testPreprocessMenuLocalTaskNodeCanonical(): void {
    $url = $this->createMock(Url::class);
    $url->method('getRouteName')->willReturn('entity.node.canonical');

    $variables = [
      'link' => ['#url' => $url, '#title' => 'View'],
      'attributes' => [],
    ];
    $this->hooks->preprocessMenuLocalTask($variables);

    $this->assertSame('Page Content', (string) $variables['link']['#title']);
    $this->assertContains('view', $variables['attributes']['class']);
    $this->assertContains('page-content-label', $variables['attributes']['class']);
  }

  /**
   * For any other route, the title is left untouched and only the
   * class-from-title is added.
   */
  public function testPreprocessMenuLocalTaskOtherRoute(): void {
    $url = $this->createMock(Url::class);
    $url->method('getRouteName')->willReturn('entity.node.edit_form');

    $variables = [
      'link' => ['#url' => $url, '#title' => 'Edit'],
      'attributes' => [],
    ];
    $this->hooks->preprocessMenuLocalTask($variables);

    $this->assertSame('Edit', $variables['link']['#title']);
    $this->assertContains('edit', $variables['attributes']['class']);
    $this->assertNotContains('page-content-label', $variables['attributes']['class']);
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
