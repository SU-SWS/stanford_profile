<?php

namespace Drupal\Tests\stanford_profile\Kernel\EventSubscriber;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\consumers\Entity\Consumer;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Site\Settings;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\default_content\Event\ImportEvent;
use Drupal\file\Entity\File;
use Drupal\KernelTests\KernelTestBase;
use Drupal\media\Entity\Media;
use Drupal\media\Entity\MediaType;
use Drupal\stanford_profile\EventSubscriber\StanfordProfileEventSubscriber;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class EventSubscriberTest.
 *
 * @group stanford_profile
 */
#[CoversClass(StanfordProfileEventSubscriber::class)]
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
    'test_stanford_profile',
    'externalauth',
    'options',
  ];

  /**
   * Event subscriber object.
   *
   * @var \Drupal\stanford_profile\EventSubscriber\StanfordProfileEventSubscriber
   */
  protected $eventSubscriber;

  /**
   * {@inheritDoc}
   */
  public function setup(): void {
    parent::setUp();
    $this->installEntitySchema('file');

    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('consumer');
    $this->installEntitySchema('oauth2_token');
    $this->installEntitySchema('media');

    $file_system = \Drupal::service('file_system');
    $logger_factory = \Drupal::service('logger.factory');
    $messenger = \Drupal::messenger();
    $client = $this->createMock(ClientInterface::class);

    $this->eventSubscriber = new TestStanfordStanfordProfileEventSubscriber($file_system, $client, $logger_factory, $messenger);

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
    $this->assertContains('onContentImport', StanfordProfileEventSubscriber::getSubscribedEvents());
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

  public function testKernelRequest() {
    $ci = getenv('CI');
    putenv('CI');

    $site_settings = [
      'stanford_capture_ownership' => TRUE,
    ];
    new Settings($site_settings);

    $config_page_loader = $this->createMock(ConfigPagesLoaderServiceInterface::class);
    $config_page_loader->method('getValue')
      ->willReturn(date(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, 0));
    \Drupal::getContainer()->set('config_pages.loader', $config_page_loader);

    $account = $this->createMock(AccountProxyInterface::class);
    $account->method('hasPermission')->willReturn(TRUE);
    $account->method('getRoles')->willReturn([]);

    \Drupal::currentUser()->setAccount($account);
    $request = Request::create('/foo/bar', 'GET', [], [], [], ['SCRIPT_NAME' => 'index.php']);

    $http_kernel = $this->createMock(HttpKernelInterface::class);
    $event = new RequestEvent($http_kernel, $request, HttpKernelInterface::MAIN_REQUEST);

    $this->eventSubscriber->onKernelRequest($event);
    $this->assertInstanceOf(RedirectResponse::class, $event->getResponse());

    if ($ci) {
      putenv("CI=$ci");
    }
  }

}

/**
 * {@inheritDoc}
 */
class TestStanfordStanfordProfileEventSubscriber extends StanfordProfileEventSubscriber {

  /**
   * {@inheritDoc}
   */
  protected function downloadFile($source, $destination) {
    file_put_contents($destination, '');
    return $destination;
  }

}
