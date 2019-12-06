<?php

namespace Drupal\stanford_profile;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StanfordProfilePermissions.
 *
 * @package Drupal\stanford_profile
 */
class StanfordProfilePermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  /**
   * StanfordProfilePermissions constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   Entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityTypeManager = $entity_manager;
  }

  /**
   * Returns an array of layout_builder per node type permissions.
   *
   * @return array
   *   A key => value array of permissions for layout library on specific nodes.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function permissions() {
    $permissions = [];
    $display_storage = $this->entityTypeManager->getStorage('entity_view_display');

    /** @var \Drupal\Core\Entity\Display\EntityDisplayInterface $display */
    foreach ($display_storage->loadMultiple() as $display) {
      if ($display->getThirdPartySetting('layout_library', 'enable')) {
        $entity_type = $display->getTargetEntityTypeId();
        $entity_bundle = $display->getTargetBundle();

        $permissions["choose layout for $entity_type $entity_bundle"] = [
          'title' => $this->t('Can choose the layout from the layout library on %entity_type: %entity_bundle', [
            '%entity_type' => $entity_type,
            '%entity_bundle' => $entity_bundle,
          ]),
        ];
      }
    }

    return $permissions;
  }

}
