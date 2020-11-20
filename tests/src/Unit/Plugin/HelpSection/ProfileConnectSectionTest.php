<?php

namespace Drupal\Tests\stanford_profile\Unit\Plugin\HelpSection;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Utility\LinkGeneratorInterface;
use Drupal\stanford_profile\Plugin\HelpSection\ProfileConnectSection;
use Drupal\Tests\UnitTestCase;

/**
 * Class ProfileConnectSectionTest
 *
 * @group stanford_profile
 * @coversDefaultClass \Drupal\stanford_profile\Plugin\HelpSection\ProfileConnectSection
 */
class ProfileConnectSectionTest extends UnitTestCase {

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $container = new ContainerBuilder();
    $container->set('string_translation', $this->getStringTranslationStub());

    $container->set('link_generator', $this->createMock(LinkGeneratorInterface::class));;
    \Drupal::setContainer($container);
  }

  /**
   * Test the connection topics exist.
   */
  public function testHelpSections() {
    $plugin = new ProfileConnectSection([], '', []);
    $topics = $plugin->listTopics();
    $this->assertCount(1, $topics);
  }

}
