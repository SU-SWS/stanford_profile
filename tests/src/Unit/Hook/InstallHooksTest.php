<?php

declare(strict_types=1);

namespace Drupal\Tests\stanford_profile\Unit\Hook;

use Drupal\config_pages\ConfigPagesInterface;
use Drupal\Core\Routing\RouteBuilderInterface;
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
   * The mocked install task manager.
   *
   * @var \Drupal\stanford_profile\InstallTaskManager|\PHPUnit\Framework\MockObject\MockObject
   */
  protected InstallTaskManager $installTaskManager;

  /**
   * The mocked route builder.
   *
   * @var \Drupal\Core\Routing\RouteBuilderInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected RouteBuilderInterface $routeBuilder;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installTaskManager = $this->createMock(InstallTaskManager::class);
    $this->routeBuilder = $this->createMock(RouteBuilderInterface::class);
    $this->hooks = new InstallHooks($this->installTaskManager, $this->routeBuilder);
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
    $install_state = ['parameters' => ['profile' => 'stanford_profile']];
    $this->installTaskManager->expects($this->once())
      ->method('runTasks')
      ->with($install_state);

    $this->hooks->finalTask($install_state);
  }

  /**
   * When no installation is being attempted, the router is never rebuilt.
   */
  public function testConfigPagesPresaveSkipsRebuildWhenNotInstalling(): void {
    unset($GLOBALS['install_state']);
    $this->routeBuilder->expects($this->never())->method('rebuild');

    $configPage = $this->createMock(ConfigPagesInterface::class);
    $this->hooks->configPagesPresave($configPage);
  }

  /**
   * When install_state exists but installation has already finished, the
   * router is not rebuilt either.
   */
  public function testConfigPagesPresaveSkipsRebuildWhenInstallationFinished(): void {
    $GLOBALS['install_state'] = ['installation_finished' => TRUE];
    $this->routeBuilder->expects($this->never())->method('rebuild');

    $configPage = $this->createMock(ConfigPagesInterface::class);
    $this->hooks->configPagesPresave($configPage);
  }

  /**
   * When an installation is actively being attempted, the router is
   * rebuilt.
   */
  public function testConfigPagesPresaveRebuildsRouterWhenInstalling(): void {
    $GLOBALS['install_state'] = ['installation_finished' => FALSE];
    $this->routeBuilder->expects($this->once())->method('rebuild');

    $configPage = $this->createMock(ConfigPagesInterface::class);
    $this->hooks->configPagesPresave($configPage);
  }

}
