<?php

namespace Drupal\cardinal_service_profile\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Deduplicate an array's values, helpful for entity reference fields.
 *
 * @code
 * process:
 *   field_term:
 *     plugin: deduplicate_array
 *     source: field_term
 *     retain_keys: false
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "deduplicate_array",
 *   handle_multiples = TRUE
 * )
 */
class DeduplicateArray extends ProcessPluginBase {

  /**
   * {@inheritDoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_array($value)) {
      $value = array_unique($value);
      $retain_keys = $this->configuration['retain_keys'] ?? FALSE;
      return $retain_keys ? $value : array_values($value);
    }
    return $value;
  }

}
