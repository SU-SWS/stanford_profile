<?php

namespace Drupal\stanford_intranet;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\File\Exception\FileException;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\State\StateInterface;
use Drupal\file\FileRepositoryInterface;

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
   * Module extension list service.
   *
   * @var \Drupal\Core\Extension\ModuleExtensionList
   */
  protected $moduleList;

  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

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
   * @param \Drupal\Core\Extension\ModuleExtensionList $module_list
   *   Module list service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FileRepositoryInterface $file_repository, FileSystemInterface $file_system, StateInterface $state, ModuleExtensionList $module_list, ConfigFactoryInterface $config_factory) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileRepository = $file_repository;
    $this->fileSystem = $file_system;
    $this->state = $state;
    $this->moduleList = $module_list;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritDoc}
   */
  public function moveIntranetFiles(): void {
    if (!$this->state->get('stanford_intranet')) {
      return;
    }
    $this->copyMediaIcons();
    $storage = $this->entityTypeManager->getStorage('file');
    $fids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('uri', 'public://%', 'LIKE')
      ->execute();

    foreach ($fids as $fid) {
      /** @var \Drupal\file\FileInterface $file */
      $file = $storage->load($fid);

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
    $this->copyMediaIcons();
  }

  /**
   * Media icons are best suited for the public directory, make sure they exist.
   *
   * @see media_install()
   */
  protected function copyMediaIcons() {
    $source = $this->moduleList->getPath('media') . '/images/icons';
    $destination = $this->configFactory->get('media.settings')
      ->get('icon_base_uri');
    $this->fileSystem->prepareDirectory($destination, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);

    $files = $this->fileSystem->scanDirectory($source, '/.*\.(svg|png|jpg|jpeg|gif)$/');
    foreach ($files as $file) {
      if (!file_exists($destination . DIRECTORY_SEPARATOR . $file->filename)) {
        try {
          $this->fileSystem->copy($file->uri, $destination, FileSystemInterface::EXISTS_ERROR);
        }
        catch (FileException $e) {
          // Ignore and continue.
        }
      }
    }
  }

}
