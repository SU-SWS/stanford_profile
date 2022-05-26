<?php

namespace Drupal\cardinal_service_profile\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\process\SkipOnValue;

/**
 * Skip the row or the process if the value equals another value in the process.
 *
 * @MigrateProcessPlugin(
 *   id = "skip_on_equal"
 * )
 */
class SkipOnEqual extends SkipOnValue {

  /**
   * Transforms the value being compared from the previous process data.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $this->configuration['value'] = $row->get($this->configuration['value']);
    return parent::transform($value, $migrate_executable, $row, $destination_property);
  }

}
