<?php

namespace Drupal\Tests\stanford_profile_permissions\Kernel\Routing;

use Drupal\KernelTests\KernelTestBase;

/**
 * Class RouteSubscriberTest.
 *
 * @group stanford_profile_permissions
 * @coversDefaultClass \Drupal\stanford_profile_permissions\Routing\RouteSubscriber
 */
class RouteSubscriberTest extends KernelTestBase {

  /**
   * {@inheritdoc}}
   */
  protected static $modules = [
    'user',
    'admin_toolbar_tools',
    'stanford_profile_permissions',
    'system',
  ];

  /**
   * {@inheritdoc}}
   */
  protected function setUp() {
    parent::setUp();
    \Drupal::service('router.builder')->rebuild();
  }

  /**
   * Make sure stanford_profile_permissions routes have been altered.
   *
   * @covers ::alterRoutes
   */
  public function testAlterRoutes() {
    /** @var \Drupal\Core\Routing\RouteProvider $route_provider */
    $route_provider = \Drupal::service('router.route_provider');
    $route = $route_provider->getRouteByName('admin_toolbar_tools.flush');
    $requirements = $route->getRequirements();
    $this->assertEquals('administer site configuration+flush caches', $requirements['_permission']);
  }

}
