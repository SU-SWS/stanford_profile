<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Cache\Context\CacheContextsManager;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\GeneratedUrl;
use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\RouteProcessor\RouteProcessorCurrent;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Routing\UrlGenerator;
use Drupal\Core\Url;
use Drupal\stanford_basic\Hook\BookHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route as SymfonyRoute;

/**
 * Unit tests for BookHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(BookHooks::class)]
class BookHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_basic\Hook\BookHooks
   */
  protected BookHooks $hooks;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->hooks = new BookHooks();
  }

  /**
   * Builds a mocked \Drupal\Core\Url object whose toString(TRUE) call
   * returns a GeneratedUrl resolving to the given path.
   */
  protected function mockUrlObject(string $generatedUrl): Url {
    $generated = $this->createMock(GeneratedUrl::class);
    $generated->method('getGeneratedUrl')->willReturn($generatedUrl);

    $url = $this->createMock(Url::class);
    $url->method('toString')->with(TRUE)->willReturn($generated);
    return $url;
  }

  /**
   * Invokes the protected setActiveBookItem() method via reflection.
   */
  protected function invokeSetActiveBookItem(array &$items, string $currentUrl): void {
    $method = new \ReflectionMethod(BookHooks::class, 'setActiveBookItem');
    $method->setAccessible(TRUE);
    $method->invokeArgs($this->hooks, [&$items, $currentUrl]);
  }

  /**
   * The '__secondary_nav' suggestion is appended, derived from the hook name.
   */
  public function testThemeSuggestionsBookTreeAlter(): void {
    $suggestions = ['book_tree'];
    $this->hooks->themeSuggestionsBookTreeAlter($suggestions, [], 'book_tree');

    $this->assertSame(['book_tree', 'book_tree__secondary_nav'], $suggestions);
  }

  /**
   * When an empty items array is given, nothing happens and no error occurs.
   */
  public function testSetActiveBookItemEmptyItems(): void {
    $items = [];
    $this->invokeSetActiveBookItem($items, '/current');

    $this->assertSame([], $items);
  }

  /**
   * A top-level item matching the current url is marked active, and
   * iteration stops (later siblings are left untouched).
   */
  public function testSetActiveBookItemTopLevelMatchStopsIteration(): void {
    $items = [
      'a' => ['url' => $this->mockUrlObject('/match')],
      'b' => ['url' => $this->mockUrlObject('/other')],
    ];
    $this->invokeSetActiveBookItem($items, '/match');

    $this->assertTrue($items['a']['is_active']);
    $this->assertArrayNotHasKey('is_active', $items['b']);
  }

  /**
   * When no top-level item matches, nested 'below' items are searched
   * recursively and the matching nested item is marked active.
   */
  public function testSetActiveBookItemNestedMatch(): void {
    $items = [
      'a' => [
        'url' => $this->mockUrlObject('/other'),
        'below' => [
          'x' => ['url' => $this->mockUrlObject('/nested-match')],
        ],
      ],
    ];
    $this->invokeSetActiveBookItem($items, '/nested-match');

    $this->assertArrayNotHasKey('is_active', $items['a']);
    $this->assertTrue($items['a']['below']['x']['is_active']);
  }

  /**
   * When nothing matches anywhere in the tree, nothing is marked active.
   */
  public function testSetActiveBookItemNoMatch(): void {
    $items = [
      'a' => [
        'url' => $this->mockUrlObject('/other'),
        'below' => [],
      ],
    ];
    $this->invokeSetActiveBookItem($items, '/no-match');

    $this->assertArrayNotHasKey('is_active', $items['a']);
  }

  /**
   * Builds a working 'url_generator' service backed by mocked dependencies,
   * configured so that Url::fromRoute('<current>') resolves to $currentPath,
   * and installs it into a fresh container via \Drupal::setContainer().
   */
  protected function installUrlGeneratorForCurrentPath(string $currentPath): void {
    $currentRoute = new SymfonyRoute($currentPath);
    $routeMatch = $this->createMock(RouteMatchInterface::class);
    $routeMatch->method('getRouteObject')->willReturn($currentRoute);
    $routeMatch->method('getRawParameters')->willReturn(new ParameterBag([]));

    $routeProcessor = new RouteProcessorCurrent($routeMatch);

    $provider = $this->createMock(RouteProviderInterface::class);
    $provider->method('getRouteByName')->with('<current>')->willReturn(new SymfonyRoute(''));

    $pathProcessor = $this->createMock(OutboundPathProcessorInterface::class);
    $pathProcessor->method('processOutbound')->willReturnArgument(0);

    $requestStack = new RequestStack();
    $requestStack->push(Request::create($currentPath));

    $urlGenerator = new UrlGenerator($provider, $pathProcessor, $routeProcessor, $requestStack);

    // RouteProcessorCurrent::processOutbound() adds the 'route' cache
    // context via addCacheContexts(), which calls Cache::mergeContexts().
    // That method validates tokens inside a PHP assert() — whether this
    // actually executes depends on the runtime zend.assertions ini setting,
    // which varies by environment. Register a real cache_contexts_manager
    // mock so the container has the service either way, rather than relying
    // on assertions being stripped.
    $cacheContextsManager = $this->getMockBuilder(CacheContextsManager::class)
      ->disableOriginalConstructor()
      ->getMock();
    $cacheContextsManager->method('assertValidTokens')->willReturn(TRUE);

    $container = new ContainerBuilder();
    $container->set('url_generator', $urlGenerator);
    $container->set('cache_contexts_manager', $cacheContextsManager);
    \Drupal::setContainer($container);
  }

  /**
   * Full happy path: preprocessBookTree() resolves the current url via
   * Url::fromRoute('<current>') and marks the matching tree item active.
   */
  public function testPreprocessBookTree(): void {
    $this->installUrlGeneratorForCurrentPath('/current-path');

    $variables = [
      'items' => [
        'a' => ['url' => $this->mockUrlObject('/current-path')],
        'b' => ['url' => $this->mockUrlObject('/other-path')],
      ],
    ];

    $this->hooks->preprocessBookTree($variables, 'book_tree');

    $this->assertTrue($variables['items']['a']['is_active']);
    $this->assertArrayNotHasKey('is_active', $variables['items']['b']);
  }

}
