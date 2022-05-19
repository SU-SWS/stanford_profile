<?php

namespace Drupal\stanford_profile\Plugin\InstallTask;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\stanford_profile\InstallTaskBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Install users task.
 *
 * @InstallTask(
 *   id="stanford_profile_users"
 * )
 */
class Users extends InstallTaskBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function runTask(array &$install_state) {
    // Modify the User 1 to match our needs.
    $admin = $this->entityTypeManager->getStorage('user')->load(1);
    if ($admin) {
      $admin->set('name', 'sws-developers');
      $admin->set('mail', 'sws-developers@lists.stanford.edu');
      $admin->addRole('administrator');
      $admin->save();
    }
  }

}
