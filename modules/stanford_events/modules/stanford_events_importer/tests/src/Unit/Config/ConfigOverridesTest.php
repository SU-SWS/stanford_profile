<?php

namespace Drupal\Tests\stanford_events_importer\Unit\Config;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\stanford_events_importer\Config\ConfigOverrides;
use Drupal\Tests\UnitTestCase;

/**
 * Config override test.
 *
 * @coversDefaultClass \Drupal\stanford_events_importer\Config\ConfigOverrides
 */
class ConfigOverridesTest extends UnitTestCase {

  protected $configPages;

  protected $configPagesValues = [];

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->configPages = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $this->configPages->method('getValue')->will($this->returnCallback([$this, 'getConfigPagesValue']));
  }

  /**
   * Test the config overrides are applied.
   */
  public function testOverrides() {
    $overridder = new ConfigOverrides($this->configPages);
    $this->assertNull($overridder->createConfigObject(''));

    $this->assertEquals([], $overridder->loadOverrides(['foobar']));

    $expected = [
      'migrate_plus.migration.stanford_localist_importer' => ['source' => ['urls' => []]],
    ];
    $this->assertEquals($expected, $overridder->loadOverrides(['migrate_plus.migration.stanford_localist_importer']));

    $this->configPagesValues = [
      'su_localist_bookmark' => ['http://example1.com'],
    ];
    $expected = [
      'migrate_plus.migration.stanford_localist_importer' => ['source' => ['urls' => ['http://example1.com']]],
    ];
    $this->assertEquals($expected, $overridder->loadOverrides(['migrate_plus.migration.stanford_localist_importer']));

    $this->configPagesValues = [
      'su_localist_url' => ['http://example2.com'],
    ];
    $expected = [
      'migrate_plus.migration.stanford_localist_importer' => ['source' => ['urls' => ['http://example2.com']]],
    ];
    $this->assertEquals($expected, $overridder->loadOverrides(['migrate_plus.migration.stanford_localist_importer']));

    $this->configPagesValues = [
      'su_localist_url' => ['http://example3.com'],
      'su_localist_bookmark' => ['http://example4.com'],
    ];
    $expected = [
      'migrate_plus.migration.stanford_localist_importer' => [
        'source' => [
          'urls' => [
            'http://example3.com',
            'http://example4.com',
          ],
        ],
      ],
    ];
    $this->assertEquals($expected, $overridder->loadOverrides(['migrate_plus.migration.stanford_localist_importer']));
  }

  /**
   * Config page loader service callback.
   */
  public function getConfigPagesValue($type, $field_name, $delta, $column) {
    return $this->configPagesValues[$field_name] ?? [];
  }

}
