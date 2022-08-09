<?php

namespace Drupal\stanford_intranet;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\State\StateInterface;
use Drupal\file\FileRepositoryInterface;
use Drupal\file\FileUsage\FileUsageInterface;

/**
 * Intranet manager service class.
 */
class StanfordIntranetManager implements StanfordIntranetManagerInterface {

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * File repository service.
   *
   * @var \Drupal\file\FileRepositoryInterface
   */
  protected $fileRepository;

  /**
   * File system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * State service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * File usage service.
   *
   * @var \Drupal\file\FileUsage\FileUsageInterface
   */
  protected $fileUsage;

  /**
   * Service constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   * @param \Drupal\file\FileRepositoryInterface $file_repository
   *   File repository service.
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   File system service.
   * @param \Drupal\Core\State\StateInterface $state
   *   State service.
   * @param \Drupal\file\FileUsage\FileUsageInterface $file_usage
   *   File usage service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FileRepositoryInterface $file_repository, FileSystemInterface $file_system, StateInterface $state, FileUsageInterface $file_usage) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileRepository = $file_repository;
    $this->fileSystem = $file_system;
    $this->state = $state;
    $this->fileUsage = $file_usage;
  }

  /**
   * {@inheritDoc}
   */
  public function moveIntranetFiles(): void {
    if (!$this->state->get('stanford_intranet')) {
      return;
    }
    $storage = $this->entityTypeManager->getStorage('file');
    $fids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('uri', 'public://%', 'LIKE')
      ->execute();

    foreach ($fids as $fid) {
      /** @var \Drupal\file\FileInterface $file */
      $file = $storage->load($fid);
      $usage = $this->fileUsage->listUsage($file);

      // Only move the files that are referenced in media entities. This will
      // exclude icons for paragraph types and media library that don't need
      // to be moved to private directory. There are no places where a file is
      // used outside a media entity, and there are no forms that provide that
      // type of access.
      if (!isset($usage['file']['media'])) {
        continue;
      }
      $uri = $file->getFileUri();
      $new_uri = str_replace('public://', 'private://', $uri);
      $directory = dirname($new_uri);

      $this->fileSystem->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
      $this->fileRepository->move($file, str_replace('public://', 'private://', $uri));
    }

    $image_styles = $this->entityTypeManager->getStorage('image_style')
      ->loadMultiple();
    foreach ($image_styles as $style) {
      $style->flush();
    }
  }

}
