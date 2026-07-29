<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_basic\Unit\Hook;

use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\stanford_basic\Hook\BreadcrumbHooks;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;

/**
 * Unit tests for BreadcrumbHooks.
 */
#[Group('stanford_basic')]
#[CoversClass(BreadcrumbHooks::class)]
class BreadcrumbHooksTest extends UnitTestCase {

  /**
   * Mocked route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected RouteMatchInterface $routeMatch;

  /**
   * Mocked title resolver.
   *
   * @var \Drupal\Core\Controller\TitleResolverInterface
   */
  protected TitleResolverInterface $titleResolver;

  /**
   * The request stack, populated with a current request.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected RequestStack $requestStack;

  /**
   * Builds the hooks object under test with the given route match.
   */
  protected function buildHooks(RouteMatchInterface $routeMatch): BreadcrumbHooks {
    return new BreadcrumbHooks($this->requestStack, $routeMatch, $this->titleResolver);
  }

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->titleResolver = $this->createMock(TitleResolverInterface::class);
    $this->requestStack = new RequestStack();
    $this->requestStack->push(Request::create('/some/path'));
  }

  /**
   * When there is no current route object, no breadcrumb entry is added.
   */
  public function testPreprocessBreadcrumbNoRouteObject(): void {
    $routeMatch = $this->createMock(RouteMatchInterface::class);
    $routeMatch->method('getRouteObject')->willReturn(NULL);
    $this->titleResolver->expects($this->never())->method('getTitle');

    $hooks = $this->buildHooks($routeMatch);

    $variables = [];
    $hooks->preprocessBreadcrumb($variables);

    $this->assertArrayNotHasKey('breadcrumb', $variables);
  }

  /**
   * When there is a current route object, the resolved page title is
   * appended to the breadcrumb array.
   */
  public function testPreprocessBreadcrumbWithRouteObject(): void {
    $route = new Route('/some/path');
    $routeMatch = $this->createMock(RouteMatchInterface::class);
    $routeMatch->method('getRouteObject')->willReturn($route);

    $this->titleResolver->expects($this->once())
      ->method('getTitle')
      ->with($this->isInstanceOf(Request::class), $route)
      ->willReturn('My Page Title');

    $hooks = $this->buildHooks($routeMatch);

    $variables = ['breadcrumb' => [['text' => 'Home']]];
    $hooks->preprocessBreadcrumb($variables);

    $this->assertSame([
      ['text' => 'Home'],
      ['text' => 'My Page Title'],
    ], $variables['breadcrumb']);
  }

}
