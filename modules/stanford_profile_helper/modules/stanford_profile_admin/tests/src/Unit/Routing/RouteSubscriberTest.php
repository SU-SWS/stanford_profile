<?php

namespace Drupal\Tests\stanford_profile_admin\Unit\Routing;

use Drupal\stanford_profile_admin\Routing\RouteSubscriber;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriberTest
 *
 * @group stanford_profile_helper
 * @coversDefaultClass \Drupal\stanford_profile_admin\Routing\RouteSubscriber
 */
class RouteSubscriberTest extends UnitTestCase {

  /**
   * The route subscriber modifies the correct routes.
   */
  public function testRoutes() {
    $route_collection = new RouteCollection();

    $route = new Route('/admin/foo');
    $route_collection->add('foo', $route);

    $route = new Route('/admin/people/bar');
    $route_collection->add('bar', $route);

    $route = new Route('/admin/bar/people');
    $route_collection->add('baz', $route);

    $subscriber = new TestRouteSubscriber();
    $subscriber->alterRoutes($route_collection);

    $this->assertEquals('/admin/foo', $route_collection->get('foo')->getPath());
    $this->assertEquals('/admin/users/bar', $route_collection->get('bar')
      ->getPath());

    $route = new Route('/admin/people');
    $route_collection->add('entity.user.collection', $route);

    $subscriber->alterRoutes($route_collection);
    $this->assertEquals('/admin/users', $route_collection->get('entity.user.collection')
      ->getPath());
    $this->assertEquals('Users', $route_collection->get('entity.user.collection')
      ->getDefault('_title'));
  }

}

/**
 * Testable route subscriber.
 */
class TestRouteSubscriber extends RouteSubscriber {

  /**
   * Make it a public method.
   */
  public function alterRoutes(RouteCollection $collection) {
    parent::alterRoutes($collection);
  }

}
