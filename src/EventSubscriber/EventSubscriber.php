<?php

namespace Drupal\stanford_profile\EventSubscriber;

use Acquia\DrupalEnvironmentDetector\AcquiaDrupalEnvironmentDetector;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\default_content\Event\DefaultContentEvents;
use Drupal\default_content\Event\ImportEvent;
use Drupal\file\FileInterface;
use Drupal\user\RoleInterface;
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
      DefaultContentEvents::IMPORT => 'onContentImport',
      EntityHookEvents::ENTITY_INSERT => 'onEntityInsert',
      EntityHookEvents::ENTITY_DELETE => 'onEntityDelete',
    ];
  }

  /**
   * EventSubscriber constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   File system service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   Logger factory service.
   */
  public function __construct(protected FileSystemInterface $fileSystem, LoggerChannelFactoryInterface $logger_factory) {
    $this->logger = $logger_factory->get('stanford_profile');
  }

  /**
   * On entity insert event.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent $event
   *   Triggered event.
   */
  public function onEntityInsert(EntityInsertEvent $event) {
    if ($event->getEntity()->getEntityTypeId() == 'user_role') {
      self::updateSamlauthRoles();
    }
  }

  /**
   * On entity delete event.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent $event
   *   Triggered event.
   */
  public function onEntityDelete(EntityDeleteEvent $event) {
    if ($event->getEntity()->getEntityTypeId() == 'user_role') {
      self::updateSamlauthRoles();
    }
  }

  /**
   * Update samlauth allowed roles settings.
   */
  protected static function updateSamlauthRoles() {
    if (!\Drupal::moduleHandler()->moduleExists('samlauth')) {
      return;
    }

    $role_ids = array_keys(user_role_names(TRUE));
    $role_ids = array_combine($role_ids, $role_ids);
    unset($role_ids[RoleInterface::AUTHENTICATED_ID]);
    asort($role_ids);

    $config = \Drupal::configFactory()->getEditable('samlauth.authentication');
    $config->set('map_users_roles', $role_ids)->save();
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
