<?php

namespace Drupal\stanford_intranet\Commands;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Password\PasswordGeneratorInterface;
use Drupal\Core\State\StateInterface;
use Drupal\externalauth\AuthmapInterface;
use Drupal\stanford_intranet\StanfordIntranetManagerInterface;
use Drush\Commands\DrushCommands;

/**
 * Stanford Intranet Drush commands.
 */
class IntranetCommands extends DrushCommands {

  /**
   * Entity Type Manager Service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * State service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * External Authentication map service.
   *
   * @var \Drupal\externalauth\AuthmapInterface
   */
  protected $authmap;

  /**
   * Core password generator service.
   *
   * @var \Drupal\Core\Password\PasswordGeneratorInterface
   */
  protected $passwordGenerator;

  /**
   * Intranet manager service.
   *
   * @var \Drupal\stanford_intranet\StanfordIntranetManagerInterface
   */
  protected $intranetManager;

  /**
   * Drush command constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity Type Manager Service.
   * @param \Drupal\Core\State\StateInterface $state
   *   State service.
   * @param \Drupal\externalauth\AuthmapInterface $authmap
   *   External Authentication map service.
   * @param \Drupal\Core\Password\PasswordGeneratorInterface $password_generator
   *   Core password generator service.
   * @param \Drupal\stanford_intranet\StanfordIntranetManagerInterface $intranet_manager
   *   Intranet manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, StateInterface $state, AuthmapInterface $authmap, PasswordGeneratorInterface $password_generator, StanfordIntranetManagerInterface $intranet_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->state = $state;
    $this->authmap = $authmap;
    $this->passwordGenerator = $password_generator;
    $this->intranetManager = $intranet_manager;
  }

  /**
   * Move files from public to the private file system.
   *
   * @command stanford-intranet:move-files
   */
  public function moveIntranetFiles() {
    $this->intranetManager->moveIntranetFiles();
  }

  /**
   * Enable and configure the intranet.
   *
   * @command stanford-intranet:setup
   * @option roles
   *   Comma delimited list of new roles to create
   * @option affiliations
   *   Comma delimited list of affiliations to limit login restrictions.
   * @option users
   *   Comma delimited list of SunetIDs to create users from.
   * @option workgroups
   *   Comma delimited list of workgroup to limit login access
   * @option role-mapping
   *   Comma delimited list of role mappings in the form `workgroup=role_name`
   * @usage stanford-intranet:setup --roles='Site Group' --workgroups=foo:bar --role-mapping='Foo Bar=site_manager'
   */
  public function setupIntranet($options = [
    'roles' => '',
    'affiliations' => '',
    'users' => '',
    'workgroups' => '',
    'role-mapping' => '',
  ]) {
    // Create the users and roles first in case we need to reference the roles
    // later.
    if (!empty($options['users'])) {
      $this->createUsers(explode(',', $options['users']));
    }
    if (!empty($options['roles'])) {
      $this->createRoles(explode(',', $options['roles']));
    }

    // Enable the intranet and clear caches. Clearing the cache tag will
    // invalidate the Varnish layer if necessary. Then clearing all the cache
    // bins to apply config overrides.
    $this->state->set('stanford_intranet', 1);
    Cache::invalidateTags(['config.system.site']);
    foreach (Cache::getBins() as $cache_backend) {
      $cache_backend->deleteAll();
    }
    $this->moveIntranetFiles();

    $config_page_storage = $this->entityTypeManager->getStorage('config_pages');
    /** @var \Drupal\config_pages\ConfigPagesInterface $config_page */
    $config_page = $config_page_storage->load('stanford_saml');
    // If the config page doesn't exist, we'll create it.
    if (!$config_page) {
      $config_page = $config_page_storage->create([
        'type' => 'stanford_saml',
        'context' => 'a:0:{}',
      ]);
    }

    // Restrict login access to specific affiliations.
    if (!empty($options['affiliations'])) {
      $config_page->set('su_simplesaml_affil', array_unique(explode(',', $options['affiliations'])));
    }
    // Restrict login access to specific workgroups..
    if (!empty($options['workgroups'])) {
      $options['workgroups'] = explode(',', $options['workgroups']);
      // Ensure `uit:sws` is the first in the list.
      array_unshift($options['workgroups'], 'uit:sws');
      $config_page->set('su_simplesaml_allowed', array_unique($options['workgroups']));
    }

    // Apply any desired role mapping.
    if (!empty($options['role-mapping'])) {
      $config_page->set('su_simplesaml_roles', $this->getRoleMappingValues(explode(',', $options['role-mapping'])));
    }

    // If there are specific login restrictions, but there are also users that
    // were created, make sure they are added to the allowed list to login.
    if ((!empty($options['affiliations']) || !empty($options['workgroups'])) && !empty($options['users'])) {
      $config_page->set('su_simplesaml_users', array_unique(explode(',', $options['users'])));
    }

    $config_page->save();
  }

  /**
   * Create users and register them with the external authentication.
   *
   * @param string[] $sunets
   *   Array of SunetsIDs.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createUsers(array $sunets) {
    $user_storage = $this->entityTypeManager->getStorage('user');
    foreach ($sunets as $sunet) {
      // If a user already exists, print a message and move along.
      if ($user_storage->loadByProperties(['name' => $sunet])) {
        $this->output()
          ->write(sprintf('User already exists with the name %s.', $sunet), TRUE);
        continue;
      }
      $new_user = $user_storage->create([
        'name' => $sunet,
        'pass' => $this->passwordGenerator->generate(15),
        'mail' => "$sunet@stanford.edu",
        'roles' => ['site_manager'],
        'status' => 1,
      ]);
      $new_user->save();
      $this->authmap->save($new_user, 'simplesamlphp_auth', $sunet);
    }
  }

  /**
   * Create any custom roles for the site.
   *
   * @param string[] $roles
   *   Array of role labels.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createRoles(array $roles) {
    $role_storage = $this->entityTypeManager->getStorage('user_role');
    foreach ($roles as $role_name) {
      $machine_name = preg_replace('/[^a-z0-9_]/', '_', strtolower($role_name));
      if ($role_storage->load("custm_$machine_name")) {
        $this->output()
          ->write(sprintf('Role already exists with the name %s.', $role_name), TRUE);
        continue;
      }
      $role_storage->create([
        'id' => "custm_$machine_name",
        'label' => $role_name,
      ])->save();
    }
  }

  /**
   * Build the pipe delimited string from the given mappings for the field.
   *
   * @param string[] $mappings
   *   Array of workgroup=role_name strings.
   *
   * @return string
   *   Pipe delimited list of role mappings.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getRoleMappingValues(array $mappings): string {
    // Always make sure uit:sws is mapped.
    $values = ['administrator:eduPersonEntitlement,=,uit:sws'];

    foreach ($mappings as $mapping) {
      [$workgroup, $role_name] = explode('=', $mapping);
      // First look up the role by the id, ex: site_manager.
      $role = $this->entityTypeManager->getStorage('user_role')
        ->load($role_name);

      // If no role was found by the ID, let's try to find it using the label.
      // ex: 'Site Manager'.
      if (!$role) {
        $roles = $this->entityTypeManager->getStorage('user_role')
          ->loadByProperties(['label' => $role_name]);
        // There should only be 1 role with a given name, so let's use that one.
        $role = !empty($roles) ? reset($roles) : NULL;
      }

      // No role was found by the ID or label. Print a message and continue to
      // the next role mapping.
      if (!$role) {
        $this->output()
          ->write(sprintf('No role found with the name %s.', $role_name), TRUE);
        continue;
      }
      $values[] = $role->id() . ":eduPersonEntitlement,=,$workgroup";
    }
    // The field widget uses pipe delimited values, same as the
    // simplesamlphp_auth module. So implode the array of role mappings into a
    // string.
    return implode("|", array_unique($values));
  }

}
