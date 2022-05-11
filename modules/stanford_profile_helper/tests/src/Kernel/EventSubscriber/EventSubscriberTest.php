<?php

namespace Drupal\Tests\stanford_profile_helper\Kernel\EventSubscriber;

use Drupal\Core\Config\ConfigImporterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;

/**
 * Class EventSubscriberTest
 *
 * @group stanford_profile_helper
 * @coversDefaultClass \Drupal\stanford_profile_helper\EventSubscriber\EventSubscriber
 */
class EventSubscriberTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'node',
    'user',
    'paragraphs',
    'react_paragraphs',
    'stanford_profile_helper',
    'config_pages',
    'layout_builder',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('paragraph');
    $this->installEntitySchema('paragraph_row');

    NodeType::create(['type' => 'stanford_page'])->save();
  }

  /**
   * We just want to make sure no errors occur during the event.
   */
  public function testConfigImportEvent() {
    $event_subscriber = \Drupal::service('stanford_profile_helper.event_subscriber');
    $events = $event_subscriber->getSubscribedEvents();
    $this->assertArrayHasKey('config.importer.import', $events);
    $event = $this->createMock(ConfigImporterEvent::class);
    // Because this event is only intended to create the node one time, lets run
    // it twice to make sure no errors occur.
    $this->assertNull($event_subscriber->onConfigImport($event));
    $this->assertNull($event_subscriber->onConfigImport($event));
  }

}
