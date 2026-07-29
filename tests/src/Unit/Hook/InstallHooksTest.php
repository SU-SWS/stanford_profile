<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_profile\Unit\Hook;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\config_pages\ConfigPagesInterface;
use Drupal\stanford_profile\Hook\InstallHooks;
use Drupal\stanford_profile\InstallTaskManager;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for InstallHooks.
 */
#[Group('stanford_profile')]
#[CoversClass(InstallHooks::class)]
class InstallHooksTest extends UnitTestCase {

  /**
   * The hook class under test.
   *
   * @var \Drupal\stanford_profile\Hook\InstallHooks
   */
  protected InstallHooks $hooks;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->hooks = new InstallHooks();
  }

  /**
   * {@inheritDoc}
   */
  protected function tearDown(): void {
    unset($GLOBALS['install_state']);
    parent::tearDown();
  }

  /**
   * finalTask() delegates to the install_tasks plugin manager's runTasks().
   */
  public function testFinalTaskRunsInstallTasks(): void {
    $installTaskManager = $this->createMock(InstallTaskManager::class);
    $installTaskManager->expects($this->once())
      ->method('runTasks')
      ->with(['parameters' => ['profile' => 'stanford_profile']]);

    $container = new ContainerBuilder();
    $container->set('plugin.manager.install_tasks', $installTaskManager);
    \Drupal::setContainer($container);

    $install_state = ['parameters' => ['profile' => 'stanford_profile']];
    InstallHooks::finalTask($install_state);
  }

  /**
   * When no installation is being attempted, the router is never rebuilt.
   */
  public function testConfigPagesPresaveSkipsRebuildWhenNotInstalling(): void {
    unset($GLOBALS['install_state']);

    $routeBuilder = $this->createMock(RouteBuilderInterface::class);
    $routeBuilder->expects($this->never())->method('rebuild');

    $container = new ContainerBuilder();
    $container->set('router.builder', $routeBuilder);
    \Drupal::setContainer($container);

    $configPage = $this->createMock(ConfigPagesInterface::class);
    $this->hooks->configPagesPresave($configPage);
  }

  /**
   * When install_state exists but installation has already finished, the
   * router is not rebuilt either.
   */
  public function testConfigPagesPresaveSkipsRebuildWhenInstallationFinished(): void {
    $GLOBALS['install_state'] = ['installation_finished' => TRUE];

    $routeBuilder = $this->createMock(RouteBuilderInterface::class);
    $routeBuilder->expects($this->never())->method('rebuild');

    $container = new ContainerBuilder();
    $container->set('router.builder', $routeBuilder);
    \Drupal::setContainer($container);

    $configPage = $this->createMock(ConfigPagesInterface::class);
    $this->hooks->configPagesPresave($configPage);
  }

  /**
   * When an installation is actively being attempted, the router is
   * rebuilt.
   */
  public function testConfigPagesPresaveRebuildsRouterWhenInstalling(): void {
    $GLOBALS['install_state'] = ['installation_finished' => FALSE];

    $routeBuilder = $this->createMock(RouteBuilderInterface::class);
    $routeBuilder->expects($this->once())->method('rebuild');

    $container = new ContainerBuilder();
    $container->set('router.builder', $routeBuilder);
    \Drupal::setContainer($container);

    $configPage = $this->createMock(ConfigPagesInterface::class);
    $this->hooks->configPagesPresave($configPage);
  }

}
