<?php

namespace Drupal\Tests\cardinal_service_profile\Kernel\Plugin\InstallTask;

use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\cardinal_service_profile\Plugin\InstallTask\RouteRebuilder;

/**
 * Class RouteRebuilderTest.
 *
 * @coversDefaultClass \Drupal\cardinal_service_profile\Plugin\InstallTask\RouteRebuilder
 */
class RouteRebuilderTest extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
    'node',
    'user',
  ];

  /**
   * {@inheritDoc}
   */
  public function setup(): void {
    parent::setUp();
    $this->setInstallProfile('cardinal_service_profile');
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installSchema('node', 'node_access');
    $this->container->set('router.builder', $this->createMock(RouteBuilderInterface::class));
  }

  public function testRouteRebuild() {
    $plugin = RouteRebuilder::create($this->container, [], '', []);
    $install_state = [];
    $this->assertNull($plugin->runTask($install_state));
  }

}
