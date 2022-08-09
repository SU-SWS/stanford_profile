<?php

namespace Drupal\stanford_profile_helper;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Serialization\Yaml;
use Drupal\Core\StringTranslation\StringTranslationTrait;

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
   * Core extension path resolver service.
   *
   * @var \Drupal\Core\Extension\ExtensionPathResolver
   */
  protected $extensionResolver;

  /**
   * Default content importer service.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Core entity type manager service.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Core config factory service.
   * @param \Drupal\Core\Extension\ExtensionPathResolver $extension_resolver
   *   Core extension path resolver.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory, ExtensionPathResolver $extension_resolver) {
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
    $this->extensionResolver = $extension_resolver;
  }

  /**
   * {@inheritDoc}
   */
  public function createDefaultContent(string $page_uuid, string $type = 'profile', string $name = ''): ?ContentEntityInterface {
    // Profile can only ever be 1 value, so ignore whatever was passed by $name.
    if ($type == 'profile') {
      $name = $this->configFactory->get('core.extension')->get('profile');
    }

    $extension_path = $this->extensionResolver->getPath($type, $name);
    $file_path = "$extension_path/content/node/$page_uuid.yml";
    $normalizer = self::getContentNormalizer();

    // Check if we have the service and the default content file exists.
    if ($normalizer && file_exists($file_path)) {
      $decoded = Yaml::decode(file_get_contents($file_path));
      $path = $decoded['default']['path'][0]['alias'];
      if ($this->pageAlreadyExists($path)) {
        return NULL;
      }

      $entity = $normalizer->denormalize($decoded);
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

  /**
   * Get the default content module normalizer service.
   *
   * @return \Drupal\default_content\Normalizer\ContentEntityNormalizerInterface|null
   *   Normalizer service.
   */
  protected static function getContentNormalizer() {
    if (\Drupal::hasService('default_content.content_entity_normalizer')) {
      return \Drupal::service('default_content.content_entity_normalizer');
    }
  }

}
