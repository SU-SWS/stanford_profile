<?php

namespace Drupal\stanford_profile_helper\EventSubscriber;

use Drupal\config_pages\ConfigPagesInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Installer\InstallerKernel;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityUpdateEvent;
use Drupal\field\FieldStorageConfigInterface;
use Drupal\menu_link_content\MenuLinkContentInterface;
use Drupal\node\NodeInterface;
use Drupal\stanford_profile_helper\StanfordDefaultContentInterface;
use Drupal\user\RoleInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Entity event subscriber service.
 */
class EntityEventSubscriber implements EventSubscriberInterface {

  use MessengerTrait;
  use StringTranslationTrait;

  /**
   * Default content importer service.
   *
   * @var \Drupal\stanford_profile_helper\StanfordDefaultContentInterface
   */
  protected $defaultContent;

  /**
   * Core state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Core entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return [
      EntityHookEvents::ENTITY_PRE_SAVE => 'onEntityPresave',
      EntityHookEvents::ENTITY_INSERT => 'onEntityInsert',
      EntityHookEvents::ENTITY_UPDATE => 'onEntityUpdate',
      EntityHookEvents::ENTITY_DELETE => 'onEntityDelete',
    ];
  }

  /**
   * Event subscriber constructor.
   *
   * @param \Drupal\stanford_profile_helper\StanfordDefaultContentInterface $stanford_default_content
   *   Default content importer service.
   * @param \Drupal\Core\State\StateInterface $state
   *   Core state service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Core entity type manager service.
   */
  public function __construct(StanfordDefaultContentInterface $stanford_default_content, StateInterface $state, EntityTypeManagerInterface $entity_type_manager) {
    $this->defaultContent = $stanford_default_content;
    $this->state = $state;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Before saving a new node, if it's the first one, create a list page.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent $event
   *   Triggered Event.
   */
  public function onEntityPresave(EntityPresaveEvent $event): void {
    $entity = $event->getEntity();
    $entity_type = $entity->getEntityTypeId();
    $method_name = 'preSave' . str_replace(' ', '', ucwords(str_replace('_', ' ', $entity_type)));
    // Call individual methods for each entity type if one is available.
    if (method_exists($this, $method_name)) {
      $this->{$method_name}($entity);
    }

    self::fixEntityUuid($entity);
  }

  /**
   * On entity insert event listener.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent $event
   *   Triggered Event.
   */
  public function onEntityInsert(EntityInsertEvent $event): void {
    $entity = $event->getEntity();
    $entity_type = $entity->getEntityTypeId();
    $method_name = 'insert' . str_replace(' ', '', ucwords(str_replace('_', ' ', $entity_type)));
    // Call individual methods for each entity type if one is available.
    if (method_exists($this, $method_name)) {
      $this->{$method_name}($entity);
    }
  }

  /**
   * On entity update event listener.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityUpdateEvent $event
   *   Triggered Event.
   */
  public function onEntityUpdate(EntityUpdateEvent $event): void {
    $entity = $event->getEntity();
    $entity_type = $entity->getEntityTypeId();
    $method_name = 'update' . str_replace(' ', '', ucwords(str_replace('_', ' ', $entity_type)));
    // Call individual methods for each entity type if one is available.
    if (method_exists($this, $method_name)) {
      $this->{$method_name}($entity, $event->getOriginalEntity());
    }
  }

  /**
   * On entity delete event listener.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent $event
   *   Triggered Event.
   */
  public function onEntityDelete(EntityDeleteEvent $event): void {
    $entity = $event->getEntity();
    $entity_type = $entity->getEntityTypeId();
    $method_name = 'delete' . str_replace(' ', '', ucwords(str_replace('_', ' ', $entity_type)));
    // Call individual methods for each entity type if one is available.
    if (method_exists($this, $method_name)) {
      $this->{$method_name}($entity);
    }
  }

  /**
   * For configuration entities, make sure the uuid matches the config file.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to fix.
   */
  protected static function fixEntityUuid(EntityInterface $entity) {
    if ($entity instanceof ConfigEntityInterface && $entity->isNew()) {
      /** @var \Drupal\Core\Config\StorageInterface $config_storage */
      $config_storage = \Drupal::service('config.storage.sync');

      // The entity exists in the config sync directory, lets check if it's uuid
      // matches.
      if (in_array($entity->getConfigDependencyName(), $config_storage->listAll())) {
        $staged_config = $config_storage->read($entity->getConfigDependencyName());

        // The uuid of the entity doesn't match that of the config in the sync
        // directory. Make sure they match so that we don't get config sync
        // issues.
        if (isset($staged_config['uuid']) && $staged_config['uuid'] != $entity->uuid()) {
          $entity->set('uuid', $staged_config['uuid']);
        }
      }
    }
  }

  /**
   * Before saving a configuration page, set some state and clear caches.
   *
   * @param \Drupal\config_pages\ConfigPagesInterface $config_page
   *   The configuration page being saved.
   */
  protected static function preSaveConfigPages(ConfigPagesInterface $config_page) {
    if (
      $config_page->hasField('su_site_url') &&
      $config_page->get('su_site_url')->count()
    ) {
      // Set the xml sitemap module state to the new domain.
      \Drupal::state()
        ->set('xmlsitemap_base_url', $config_page->get('su_site_url')
          ->get(0)
          ->get('uri')
          ->getString());
    }

    // Invalidate cache tags on config pages save. This is a blanket cache clear
    // since config pages mostly affect the entire site.
    Cache::invalidateTags([
      'config:system.site',
      'system.site',
      'block_view',
      'node_view',
    ]);
  }

  /**
   * Before saving a field storage, adjust the third party settings.
   *
   * @param \Drupal\field\FieldStorageConfigInterface $field_storage
   *   Field storage being saved.
   */
  protected static function preSaveFieldStorageConfig(FieldStorageConfigInterface $field_storage) {
    // If a field is saved and the field permissions are public, lets just
    // remove those third party settings before save so that it keeps the
    // config clean.
    if ($field_storage->getThirdPartySetting('field_permissions', 'permission_type') === 'public') {
      $field_storage->unsetThirdPartySetting('field_permissions', 'permission_type');
      $field_storage->calculateDependencies();
    }
  }

  /**
   * Before saving a menu item, clear caches.
   *
   * @param \Drupal\menu_link_content\MenuLinkContentInterface $entity
   *   Menu item being saved.
   */
  protected function insertMenuLinkContent(MenuLinkContentInterface $entity) {
    Cache::invalidateTags(['stanford_profile_helper:menu_links']);
  }

  /**
   * When deleting a menu item, clear caches.
   *
   * @param \Drupal\menu_link_content\MenuLinkContentInterface $entity
   *   Menu item being deleted.
   */
  protected function deleteMenuLinkContent(MenuLinkContentInterface $entity) {
    Cache::invalidateTags(['stanford_profile_helper:menu_links']);
  }

  /**
   * When updating a menu item, clear caches if necessary.
   *
   * @param \Drupal\menu_link_content\MenuLinkContentInterface $entity
   *   Modified menu item.
   * @param \Drupal\menu_link_content\MenuLinkContentInterface $original_entity
   *   Original unmodified menu item.
   */
  protected function updateMenuLinkContent(MenuLinkContentInterface $entity, MenuLinkContentInterface $original_entity) {
    $original = [
      $original_entity->get('title')->getValue(),
      $original_entity->get('description')->getValue(),
      $original_entity->get('link')->getValue(),
      $original_entity->get('parent')->getValue(),
      $original_entity->get('weight')->getValue(),
      $original_entity->get('expanded')->getValue(),
    ];
    $updated = [
      $entity->get('title')->getValue(),
      $entity->get('description')->getValue(),
      $entity->get('link')->getValue(),
      $entity->get('parent')->getValue(),
      $entity->get('weight')->getValue(),
      $entity->get('expanded')->getValue(),
    ];
    if (md5(json_encode($original)) != md5(json_encode($updated))) {
      Cache::invalidateTags(['stanford_profile_helper:menu_links']);
    }
  }

  /**
   * Before saving a menu item, adjust the path if an internal path exists.
   *
   * @param \Drupal\menu_link_content\MenuLinkContentInterface $entity
   *   The menu link being saved.
   */
  protected static function preSaveMenuLinkContent(MenuLinkContentInterface $entity): void {
    $cache_tags = [];

    $destination = $entity->get('link')->getString();
    if ($internal_path = self::lookupInternalPath($destination)) {
      $entity->set('link', $internal_path);
    }

    // For new menu link items created on a node form (normally), set the
    // expanded attribute so all menu items are expanded by default.
    if ($entity->isNew()) {
      $entity->set('expanded', TRUE);
    }

    // When a menu item is added as a child of another menu item clear the
    // parent pages cache so that the block shows up as it doesn't get
    // invalidated just by the menu cache tags.
    $parent_id = $entity->getParentId();
    if (!empty($parent_id)) {
      [$entity_name, $uuid] = explode(':', $parent_id);
      $menu_link_content = \Drupal::entityTypeManager()
        ->getStorage($entity_name)
        ->loadByProperties(['uuid' => $uuid]);

      if (is_array($menu_link_content)) {
        $parent_item = array_pop($menu_link_content);
        /** @var \Drupal\Core\Url $url */
        $url = $parent_item->getUrlObject();
        if (!$url->isExternal() && $url->isRouted()) {
          $params = $url->getRouteParameters();
          if (isset($params['node'])) {
            $cache_tags[] = 'node:' . $params['node'];
          }
        }
      }
    }

    Cache::invalidateTags($cache_tags);
  }

  /**
   * Before saving a redirect, adjust the path if an internal path exists.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   Redirect to be saved.
   */
  protected function preSaveRedirect(ContentEntityInterface $entity): void {
    $destination = $entity->get('redirect_redirect')->getString();
    if ($internal_path = self::lookupInternalPath($destination)) {
      $entity->set('redirect_redirect', $internal_path);
    }
  }

  /**
   * Before saving a node, if a default content list page exists, create it.
   *
   * @param \Drupal\node\NodeInterface $entity
   *   The node being saved.
   */
  protected function preSaveNode(NodeInterface $entity): void {
    // Invalidate any search result cached so the updated/new content will be
    // displayed for previously searched terms.
    Cache::invalidateTags(['config:views.view.search']);

    if (
      InstallerKernel::installationAttempted() ||
      !$entity->isNew()
    ) {
      return;
    }

    $pages = [
      'stanford_news' => '0b83d1e9-688a-4475-9673-a4c385f26247',
      'stanford_event' => '8ba98fcf-d390-4014-92de-c77a59b30f3b',
      'stanford_person' => '673a8fb8-39ac-49df-94c2-ed8d04db16a7',
      'stanford_course' => '14768832-f763-4d27-8df6-7cd784886d57',
    ];
    $bundle = $entity->bundle();
    $state_key = 'stanford_profile_helper.default_content.' . $bundle;

    if (
      array_key_exists($bundle, $pages) &&
      !$this->state->get($state_key)
    ) {
      $this->state->set($state_key, TRUE);
      $count = $this->entityTypeManager->getStorage('node')
        ->getQuery()
        ->accessCheck(FALSE)
        ->condition('type', $bundle)
        ->count()
        ->execute();

      if ((int) $count == 0) {
        $new_entity = $this->defaultContent->createDefaultContent($pages[$bundle]);
        if ($new_entity) {
          $this->messenger()
            ->addMessage($this->t('A new page was created automatically for you. View the @link page to make changes.', [
              '@link' => Link::fromTextAndUrl($new_entity->label(), $new_entity->toUrl())
                ->toString(),
            ]));
        }
      }
    }
  }

  /**
   * Before saving a user role, prepend it with `custm_`.
   *
   * @param \Drupal\user\RoleInterface $role
   *   The role being saved.
   */
  protected static function preSaveUserRole(RoleInterface $role) {
    /** @var \Drupal\Core\Config\StorageInterface $config_storage */
    $config_storage = \Drupal::service('config.storage.sync');

    // Only modify new roles if they are created through the UI and don't exist
    // in the config management - Prefix them with "custm_" so they can be
    // easily identifiable.
    if (
      PHP_SAPI != 'cli' &&
      $role->isNew() &&
      !in_array($role->getConfigDependencyName(), $config_storage->listAll())
    ) {
      $role->set('id', 'custm_' . $role->id());
    }
  }

  /**
   * Lookup an internal path.
   *
   * @param string $uri
   *   The destination path.
   *
   * @return string|null
   *   The internal path, or NULL if not found.
   */
  protected static function lookupInternalPath(string $uri): ?string {
    // If a redirect is added to go to the aliased path of a node (often from
    // importing redirect), change the destination to target the node instead.
    // This works if the destination is `/about` or `/node/9`.
    if (preg_match('/^internal:(\/.*)/', $uri, $matches)) {
      // Find the internal path from the alias.
      $path = \Drupal::service('path_alias.manager')
        ->getPathByAlias($matches[1]);

      // Grab the node id from the internal path and use that as destination.
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        return 'entity:node/' . $matches[1];
      }
    }
    return NULL;
  }

}
