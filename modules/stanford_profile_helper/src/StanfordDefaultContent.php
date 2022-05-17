<?php

namespace Drupal\stanford_profile_helper;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ExtensionList;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\default_content\Normalizer\ContentEntityNormalizerInterface;

/**
 * Default content importer service to import specific content by uuid.
 */
class StanfordDefaultContent implements StanfordDefaultContentInterface {

  use MessengerTrait;
  use StringTranslationTrait;

  /**
   * Core entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Core config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Core profile extension list service.
   *
   * @var \Drupal\Core\Extension\ExtensionList
   */
  protected $profileExtensionList;

  /**
   * Default content normalizer.
   *
   * @var \Drupal\default_content\Normalizer\ContentEntityNormalizerInterface
   */
  protected $contentNormalizer;

  /**
   * Default content importer service.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Core entity type manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Core config factory service.
   * @param \Drupal\Core\Extension\ExtensionList $profile_extensions
   *   Core profile extension list service.
   * @param \Drupal\default_content\Normalizer\ContentEntityNormalizerInterface $normalizer
   *   Default content normalizer.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory, ExtensionList $profile_extensions, ContentEntityNormalizerInterface $normalizer) {
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
    $this->profileExtensionList = $profile_extensions;
    $this->contentNormalizer = $normalizer;
  }

  /**
   * {@inheritDoc}
   */
  public function createDefaultListPage($page_uuid): ?ContentEntityInterface {
    $current_profile = $this->configFactory->get('core.extension')
      ->get('profile');
    $profile_path = $this->profileExtensionList->getPath($current_profile);
    $file_path = "$profile_path/content/node/$page_uuid.yml";

    if (file_exists($file_path)) {
      $decoded = Yaml::decode(file_get_contents($file_path));
      $path = $decoded['default']['path'][0]['alias'];
      if ($this->pageAlreadyExists($path)) {
        return NULL;
      }

      $entity = $this->contentNormalizer->denormalize($decoded);
      $entity->save();
      return $entity;
    }
    return NULL;
  }

  /**
   * Check if a page with the given path already exists.
   *
   * @param string $path
   *   Aliased path.
   *
   * @return bool
   *   If a path alias already exists.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function pageAlreadyExists(string $path): bool {
    $alias_storage = $this->entityTypeManager->getStorage('path_alias');
    return !empty($alias_storage->loadByProperties(['alias' => $path]));
  }

}
