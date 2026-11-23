<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_profile\Unit\Hook;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\config_pages\ConfigPagesInterface;
use Drupal\stanford_profile\Hook\InstallHooks;
use Drupal\stanford_profile\InstallTaskManager;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Unit tests for InstallHooks.
 */
#[Group('stanford_profile')]
class InstallHooksTest extends UnitTestCase {

  /**
   * {@inheritDoc}
   */
  protected function tearDown(): void {
    unset($GLOBALS['install_state']);
    parent::tearDown();
  }

  /**
   * FinalTask() delegates to the install_tasks plugin manager's runTasks().
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

    $configPage = $this->createMock(ConfigPagesInterface::class);
    (new InstallHooks($routeBuilder))->configPagesPresave($configPage);
  }

  /**
   * Test the router is not rebuilt once installation has finished.
   */
  public function testConfigPagesPresaveSkipsRebuildWhenInstallationFinished(): void {
    $GLOBALS['install_state'] = ['installation_finished' => TRUE];

    $routeBuilder = $this->createMock(RouteBuilderInterface::class);
    $routeBuilder->expects($this->never())->method('rebuild');

    $configPage = $this->createMock(ConfigPagesInterface::class);
    (new InstallHooks($routeBuilder))->configPagesPresave($configPage);
  }

  /**
   * Test the router is rebuilt while an installation is in progress.
   */
  public function testConfigPagesPresaveRebuildsRouterWhenInstalling(): void {
    $GLOBALS['install_state'] = ['installation_finished' => FALSE];

    $routeBuilder = $this->createMock(RouteBuilderInterface::class);
    $routeBuilder->expects($this->once())->method('rebuild');

    $configPage = $this->createMock(ConfigPagesInterface::class);
    (new InstallHooks($routeBuilder))->configPagesPresave($configPage);
  }

}
