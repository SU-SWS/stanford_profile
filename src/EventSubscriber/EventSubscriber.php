<?php

namespace Drupal\stanford_profile\EventSubscriber;

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
  public function onContentImport(ImportEvent $event) {
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
  protected function getFile($file_uri) {
    $local_directory = substr($file_uri, 0, strrpos($file_uri, '/'));
    $this->fileSystem->prepareDirectory($local_directory, FileSystemInterface::CREATE_DIRECTORY);

    $file_scheme = StreamWrapperManager::getScheme($file_uri);
    $file_path = str_replace("$file_scheme://", '', $file_uri);
    $local_file = DRUPAL_ROOT . $this::FETCH_DIR . $file_path;

    // @codeCoverageIgnoreStart
    if (file_exists($local_file)) {
      try {
        $this->fileSystem->copy($local_file, $file_path, FileSystemInterface::EXISTS_REPLACE);
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
    $this->downloadFile($this::FETCH_DOMAIN . $this::FETCH_DIR . $file_path, $file_uri);
  }

  /**
   * Download the file and place it in the given location.
   *
   * @param string $source
   *   File url source.
   * @param string $destination
   *   Local path with schema.
   *
   * @codeCoverageIgnore
   *   Ignore from unit tests.
   */
  protected function downloadFile($source, $destination) {
    system_retrieve_file($source, $destination, FALSE, FileSystemInterface::EXISTS_REPLACE);
  }

}
