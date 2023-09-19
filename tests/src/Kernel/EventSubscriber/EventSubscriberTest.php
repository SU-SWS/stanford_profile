<?php

namespace Drupal\Tests\stanford_profile\Kernel\EventSubscriber;

use Drupal\consumers\Entity\Consumer;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Drupal\default_content\Event\ImportEvent;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\file\Entity\File;
use Drupal\KernelTests\KernelTestBase;
use Drupal\media\Entity\Media;
use Drupal\media\Entity\MediaType;
use Drupal\stanford_profile\EventSubscriber\EventSubscriber as StanfordEventSubscriber;

/**
 * Class EventSubscriberTest.
 *
 * @group stanford_profile
 * @coversDefaultClass \Drupal\stanford_profile\EventSubscriber\EventSubscriber
 */
class EventSubscriberTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'node',
    'user',
    'consumers',
    'default_content',
    'field',
    'image',
    'file',
    'simple_oauth',
    'serialization',
    'media',
  ];

  /**
   * Event subscriber object.
   *
   * @var \Drupal\stanford_profile\EventSubscriber\EventSubscriber
   */
  protected $eventSubscriber;

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('file');

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('consumer');
    $this->installEntitySchema('oauth2_token');
    $this->installEntitySchema('media');


    $file_system = $this->createMock(FileSystemInterface::class);
    $logger_factory = $this->createMock(LoggerChannelFactoryInterface::class);

    $this->eventSubscriber = new TestStanfordEventSubscriber($file_system, $logger_factory);

    /** @var \Drupal\media\MediaTypeInterface $media_type */
    $media_type = MediaType::create([
      'id' => 'image',
      'label' => 'image',
      'source' => 'image',
    ]);
    $media_type->save();

    // Create the source field.
    $source_field = $media_type->getSource()->createSourceField($media_type);
    $source_field->getFieldStorageDefinition()->save();
    $source_field->save();
    $media_type->set('source_configuration', [
      'source_field' => $source_field->getName(),
    ])->save();
  }

  /**
   * Test the consumer secret is randomized.
   */
  public function testConsumerSecretRandomized() {
    $this->assertContains('onContentImport', StanfordEventSubscriber::getSubscribedEvents());
    $consumer = Consumer::create([
      'client_id' => 'foobar',
      'label' => 'foobar',
      'secret' => 'foobar',
    ]);
    $consumer->save();
    $secret = $consumer->get('secret')->getString();
    $this->assertNotEquals('foobar', $secret);
    $event = new ImportEvent([$consumer], 'foobar');
    $this->eventSubscriber->onContentImport($event);
    $this->assertNotEquals($secret, $consumer->get('secret')->getString());
  }

  public function testContentImportEntity() {
    $file = File::create(['uri' => 'public://foobar.jpg']);
    $file->save();

    $this->assertFileDoesNotExist('public://foobar.jpg');

    /** @var \Drupal\media\MediaInterface $media */
    $media = Media::create([
      'bundle' => 'image',
      'field_media_image' => ['target_id' => $file->id()],
    ]);
    $event = new ImportEvent([$media], 'foobar');
    $this->eventSubscriber->onContentImport($event);

    $this->assertFileExists('public://foobar.jpg');
  }

}

/**
 * {@inheritDoc}
 */
class TestStanfordEventSubscriber extends StanfordEventSubscriber {

  /**
   * {@inheritDoc}
   */
  protected function downloadFile($source, $destination) {
    file_put_contents($destination, '');
    return $destination;
  }

}
