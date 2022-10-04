<?php

namespace Drupal\stanford_profile\EventSubscriber;

use Acquia\DrupalEnvironmentDetector\AcquiaDrupalEnvironmentDetector;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Drupal\default_content\Event\DefaultContentEvents;
use Drupal\default_content\Event\ImportEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EventSubscriber.
 *
 * @package Drupal\stanford_profile\EventSubscriber
 */
class EventSubscriber implements EventSubscriberInterface {

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
   * File system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

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
    return [DefaultContentEvents::IMPORT => 'onContentImport'];
  }

  /**
   * EventSubscriber constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   File system service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Logger factory service.
   */
  public function __construct(FileSystemInterface $file_system, LoggerChannelFactoryInterface $logger_factory) {
    $this->fileSystem = $file_system;
    $this->logger = $logger_factory->get('stanford_profile');
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
      if ($entity->getEntityTypeId() != 'file') {
        continue;
      }

      $file_uri = $entity->getFileUri();

      if (!file_exists($file_uri)) {
        $this->getFile($file_uri);
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
        $this->fileSystem->copy($local_file, $file_uri, FileSystemInterface::EXISTS_REPLACE);
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
    return system_retrieve_file($source, $destination, FALSE, FileSystemInterface::EXISTS_REPLACE);
  }

}
