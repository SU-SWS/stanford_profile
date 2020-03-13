<?php

namespace Drupal\cardinal_service_profile;

/**
 * Install task plugin interface.
 *
 * @package Drupal\cardinal_service_profile
 */
interface InstallTaskInterface {

  /**
   * Perform some install tasks.
   *
   * @param array $install_state
   *   Current install state.
   *
   * @see hook_install_tasks()
   */
  public function runTask(array &$install_state);

}
