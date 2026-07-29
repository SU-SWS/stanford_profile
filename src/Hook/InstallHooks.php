<?php

declare(strict_types=1);

namespace Drupal\stanford_profile\Hook;

use Drupal\config_pages\ConfigPagesInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Installer\InstallerKernel;
use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\stanford_profile\InstallTaskManager;

/**
 * Hooks that relate to installing the stanford_profile install profile.
 *
 * hook_install_tasks() itself cannot be implemented with the #[Hook]
 * attribute (Drupal core's HookCollectorPass::checkForProceduralOnlyHooks()
 * explicitly denies it, along with hook_install(), hook_schema(), and a few
 * others) so it remains procedural in stanford_profile.profile. It resolves
 * a DI-wired instance of this class via \Drupal::classResolver() to call
 * self::finalTask(), which is not itself a hook.
 */
class InstallHooks {

  public function __construct(
    protected InstallTaskManager $installTaskManager,
    protected RouteBuilderInterface $routeBuilder,
  ) {}

  /**
   * Perform final tasks after the profile has completed installing.
   *
   * @param array $install_state
   *   Current install state.
   */
  public function finalTask(array &$install_state): void {
    $this->installTaskManager->runTasks($install_state);
  }

  /**
   * Implements hook_ENTITY_TYPE_presave().
   *
   * During install, rebuild the router when saving a config page. This
   * prevents an error if the config page route doesn't exist for it yet.
   * Event subscriber doesn't work for this since it's during installation.
   */
  #[Hook('config_pages_presave')]
  public function configPagesPresave(ConfigPagesInterface $config_page): void {
    if (InstallerKernel::installationAttempted()) {
      $this->routeBuilder->rebuild();
    }
  }

}
