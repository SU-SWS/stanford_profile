<?php

namespace Drupal\stanford_profile\Plugin\InstallTask;

use Drupal\Component\Utility\Html;
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
    foreach (glob(__DIR__ . '/users/*.json') as $user_file) {
      $user_data = json_decode(file_get_contents($user_file), TRUE);
      $roles = array_map(function ($role) {
        return $role['target_id'];
      }, $user_data['roles']);
      $this->addUser($user_data['name'][0]['value'], $user_data['mail'][0]['value'], $roles);
    }

    // Modify the User 1 to match our needs.
    $admin = $this->entityTypeManager->getStorage('user')->load(1);
    $admin->set('name', 'sws-developers');
    $admin->set('mail', 'sws-developers@lists.stanford.edu');
    $admin->addRole('administrator');
    $admin->save();
  }

  /**
   * Create a new Drupal user.
   *
   * @param string $name
   *   User name.
   * @param string $email
   *   User email.
   * @param array $roles
   *   Array of roles for the user.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function addUser($name, $email, array $roles = []) {
    $this->entityTypeManager->getStorage('user')->create([
      'name' => Html::escape($name),
      'mail' => $email,
      'roles' => $roles,
      'status' => TRUE,
    ])->save();
  }

}
