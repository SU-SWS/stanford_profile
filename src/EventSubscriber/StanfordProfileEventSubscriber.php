<?php

namespace Drupal\stanford_profile\EventSubscriber;

use Acquia\DrupalEnvironmentDetector\AcquiaDrupalEnvironmentDetector;
use Drupal\Core\File\FileExists;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Drupal\Core\Url;
use Drupal\default_content\Event\ImportEvent;
use Drupal\file\FileInterface;
use Drupal\stanford_profile_helper\Hook\ConfigPagesHooks;
use GuzzleHttp\ClientInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class EventSubscriber.
 *
 * @package Drupal\stanford_profile\EventSubscriber
 */
class StanfordProfileEventSubscriber implements EventSubscriberInterface {

  /**
   * External site url to fetch the given file from.
   */
  const FETCH_DOMAIN = 'https://content.sites.stanford.edu';

  /**
   * The directory the file lives on the server.
   *
   * This should be the same location in the FETCH_DOMAIN as it is when trying
   * to copy locally.
   */
  const FETCH_DIR = '/sites/default/files/';

  /**
   * Logger channel service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return [
      'default_content.import' => 'onContentImport',
      KernelEvents::REQUEST => 'onKernelRequest',
    ];
  }

  /**
   * EventSubscriber constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   File system service.
   * @param \GuzzleHttp\ClientInterface $client
   *   Guzzle client service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Logger factory service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   Messenger service.
   */
  public function __construct(protected FileSystemInterface $fileSystem, protected ClientInterface $client, LoggerChannelFactoryInterface $logger_factory, protected MessengerInterface $messenger) {
    $this->logger = $logger_factory->get('stanford_profile');
  }

  /**
   * On kernel request, redirect the user to update contact information.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   Triggered event.
   */
  public function onKernelRequest(RequestEvent $event) {
    $current_uri = $event->getRequest()->getRequestUri();

    if (
      $event->getRequestType() == HttpKernelInterface::MAIN_REQUEST &&
      (Settings::get('stanford_capture_ownership', FALSE)) &&
      !str_starts_with($current_uri, '/admin/config/system/basic-site-settings') &&
      ConfigPagesHooks::redirectUser()
    ) {
      $config_page_url = Url::fromRoute('config_pages.stanford_basic_site_settings', [], ['query' => ['destination' => $current_uri]])
        ->toString(TRUE)
        ->getGeneratedUrl();
      $this->messenger->addWarning('Please update or verify the site contact information on the "Site Contacts" tab.');
      $event->setResponse(new RedirectResponse($config_page_url . '#contact'));
    }
  }

  /**
   * When content is imported, download the images.
   *
   * @param \Drupal\default_content\Event\ImportEvent $event
   *   Triggered event.
   */
  public function onContentImport(ImportEvent $event): void {
    /** @var \Drupal\file\FileInterface $entity */
    foreach ($event->getImportedEntities() as $entity) {
      if ($entity->getEntityTypeId() == 'consumer') {
        $entity->set('secret', md5(random_int(0, 99999)));
        $entity->save();
      }

      if ($entity->getEntityTypeId() == 'media') {
        foreach ($entity->getFieldDefinitions() as $field) {
          if ($field->getType() == 'image') {
            foreach ($entity->get($field->getName()) as $item) {
              if (!$item->entity instanceof FileInterface) {
                continue;
              }

              $file_uri = $item->entity->getFileUri();

              if (!file_exists($file_uri)) {
                $this->getFile($file_uri);
              }

              [$width, $height] = @getimagesize($file_uri);
              $item->set('width', (int) $width);
              $item->set('height', (int) $height);
            }
          }
        }
      }
    }
  }

  /**
   * Get the file from another directory, or from fetching the URL.
   *
   * @param string $file_uri
   *   Local file path with schema.
   */
  protected function getFile(string $file_uri): void {
    $local_directory = dirname($file_uri);
    $this->fileSystem->prepareDirectory($local_directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);

    $file_scheme = StreamWrapperManager::getScheme($file_uri);
    $file_path = str_replace("$file_scheme://", '', $file_uri);
    $remote_url = $this::FETCH_DOMAIN . $this::FETCH_DIR . $file_path;

    $local_file = AcquiaDrupalEnvironmentDetector::getAhFilesRoot() . $this::FETCH_DIR . $file_path;

    // @codeCoverageIgnoreStart
    if (file_exists($local_file)) {
      try {
        $this->logger->info('Copying local file %source to %destination', [
          '%source' => $local_file,
          '%destination' => $file_uri,
        ]);
        $this->fileSystem->copy($local_file, $file_uri, FileExists::Replace);
        return;
      }
      catch (\Exception $e) {
        $this->logger->error('Unable to copy local file %file. Message: %message', [
          '%file' => basename($file_uri),
          '%message' => $e->getMessage(),
        ]);
      }
    }

    // @codeCoverageIgnoreEnd
    // Fallback to download the file from the remote url.
    $this->downloadFile($remote_url, $file_uri);
  }

  /**
   * Download the file and place it in the given location.
   *
   * @param string $source
   *   File url source.
   * @param string $destination
   *   Local path with schema.
   *
   * @return mixed
   *   See system_retrieve_file().
   *
   * @codeCoverageIgnore
   *   Ignore from unit tests.
   */
  protected function downloadFile(string $source, string $destination) {
    $this->logger->info('Downloading file %source to %destination', [
      '%source' => $source,
      '%destination' => $destination,
    ]);
    $data = (string) $this->client->get($source)->getBody();
    return $this->fileSystem->saveData($data, $destination, FileExists::Replace);
  }

}
