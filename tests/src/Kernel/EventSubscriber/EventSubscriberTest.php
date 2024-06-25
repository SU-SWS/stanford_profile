<?php

namespace Drupal\Tests\stanford_profile\Kernel\EventSubscriber;

use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\consumers\Entity\Consumer;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\Core\Site\Settings;
use Drupal\default_content\Event\ImportEvent;
use Drupal\file\Entity\File;
use Drupal\KernelTests\KernelTestBase;
use Drupal\media\Entity\Media;
use Drupal\media\Entity\MediaType;
use Drupal\stanford_profile\EventSubscriber\EventSubscriber as StanfordEventSubscriber;
use Drupal\user\Entity\Role;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

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
    'test_stanford_profile',
    'samlauth',
    'externalauth'
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

    $this->eventSubscriber = new TestStanfordEventSubscriber($file_system, $logger_factory, $messenger);

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

  public function testUserInsert() {
    $role = Role::create(['id' => 'test_role1', 'label' => 'Test role 1']);
    $role->save();

    $event = new EntityInsertEvent($role);
    $this->eventSubscriber->onEntityInsert($event);
    $saml_setting = \Drupal::config('samlauth.authentication')
      ->get('map_users_roles');

    $this->assertContains('test_role1', $saml_setting);
  }

  public function testKernelRequest() {
    $ci = getenv('CI');
    putenv('CI');

    $site_settings = [
      'stanford_capture_ownership' => TRUE,
    ];
    new Settings($site_settings);

    $config_page_loader = $this->createMock(ConfigPagesLoaderServiceInterface::class);
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
class TestStanfordEventSubscriber extends StanfordEventSubscriber {

  /**
   * {@inheritDoc}
   */
  protected function downloadFile($source, $destination) {
    file_put_contents($destination, '');
    return $destination;
  }

}
