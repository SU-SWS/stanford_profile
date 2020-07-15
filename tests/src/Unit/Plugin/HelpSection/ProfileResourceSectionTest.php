<?php

namespace Drupal\Tests\cardinal_service_profile\Unit\Plugin\HelpSection;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\cardinal_service_profile\Plugin\HelpSection\ProfileConnectSection;
use Drupal\cardinal_service_profile\Plugin\HelpSection\ProfileResourceSection;
use Drupal\Tests\UnitTestCase;

/**
 * Class ProfileConnectSectionTest
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_profile\Plugin\HelpSection\ProfileResourceSection
 */
class ProfileResourceSectionTest extends UnitTestCase {

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $container = new ContainerBuilder();
    $container->set('string_translation', $this->getStringTranslationStub());

    $container->set('link_generator', $this->createMock(LinkGeneratorInterface::class));
    \Drupal::setContainer($container);
  }

  /**
   * Test the connection topics exist.
   */
  public function testHelpSections() {
    $plugin = new ProfileResourceSection([], '', []);
    $topics = $plugin->listTopics();
    $this->assertCount(4, $topics);
  }

}
