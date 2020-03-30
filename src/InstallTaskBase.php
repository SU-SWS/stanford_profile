<?php

namespace Drupal\stanford_profile;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Base class for install task plugins.
 *
 * @package Drupal\stanford_profile
 */
abstract class InstallTaskBase extends PluginBase implements InstallTaskInterface {

  use StringTranslationTrait;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Is the install occurring on Acquia environment.
   *
   * @return bool
   *   True if on Acquia.
   *
   * @codeCoverageIgnore
   *   We want to test the class and need to fake being on Acquia.
   */
  protected static function isAhEnv() {
    return isset($_ENV['AH_SITE_ENVIRONMENT']);
  }

}
