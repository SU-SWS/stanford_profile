<?php

namespace Drupal\Tests\cardinal_service_profile\Unit\Plugin\HelpSection;

use Drupal\cardinal_service_profile\Plugin\HelpSection\CardinalServiceSection;
use Drupal\Tests\UnitTestCase;

/**
 * Class CardinalServiceSectionTest
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_profile\Plugin\HelpSection\CardinalServiceSection
 */
class CardinalServiceSectionTest extends UnitTestCase {

  /**
   * The help section should return some links.
   */
  public function testPlugin() {
    $plugin = new TestCardinalServiceSection([], '', []);
    $topics = $plugin->listTopics();
    $this->assertNotEmpty($topics);
  }

}

class TestCardinalServiceSection extends CardinalServiceSection {

  protected static function getProfilePath() {
    return dirname(__DIR__, 5);
  }

}
