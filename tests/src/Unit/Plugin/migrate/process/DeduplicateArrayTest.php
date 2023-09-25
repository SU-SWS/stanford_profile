<?php

namespace Drupal\Tests\cardinal_service_profile\Unit\Plugin\migrate\process;

use Drupal\cardinal_service_profile\Plugin\migrate\process\DeduplicateArray;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\Row;
use Drupal\Tests\UnitTestCase;

/**
 * Class DeduplicateArrayTest.
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_profile\Plugin\migrate\process\DeduplicateArray
 */
class DeduplicateArrayTest extends UnitTestCase {

  /**
   * Plugin should remove duplicates.
   */
  public function testDeduplicatePlugin() {
    $plugin = new DeduplicateArray([], '', []);
    $value = [
      'foo',
      'bar',
      'foo',
      'baz',
    ];
    $migration = $this->createMock(MigrateExecutable::class);
    $row = $this->createMock(Row::class);
    $new_value = $plugin->transform($value, $migration, $row, NULL);
    $this->assertEquals(['foo', 'bar', 'baz'], $new_value);
  }

}
