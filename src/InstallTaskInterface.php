<?php

namespace Drupal\stanford_profile;

/**
 * Interface InstallTaskInterface
 *
 * @package Drupal\stanford_profile
 */
interface InstallTaskInterface {

  public function runTask(&$install_state);

}
