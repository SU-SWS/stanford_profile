<?php

namespace Drupal\Tests\stanford_profile\Kernel\Plugin\InstallTask;

use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\stanford_profile\Plugin\InstallTask\RouteRebuilder;

/**
 * Class RouteRebuilderTest.
 *
 * @coversDefaultClass \Drupal\stanford_profile\Plugin\InstallTask\RouteRebuilder
 */
class RouteRebuilderTest extends KernelTestBase {

  /**
   * {@inheritDoc}
   */
  protected static $modules = [
    'system',
  ];

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->setInstallProfile('stanford_profile');
    $this->container->set('router.builder', $this->createMock(RouteBuilderInterface::class));
  }

  public function testRouteRebuild() {
    $plugin = RouteRebuilder::create($this->container);
    $install_state = [];
    $this->assertNull($plugin->runTask($install_state));
  }

}
