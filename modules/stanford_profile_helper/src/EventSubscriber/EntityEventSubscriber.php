<?php

namespace Drupal\stanford_profile_helper\EventSubscriber;

use Drupal\config_pages\ConfigPagesInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Installer\InstallerKernel;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Routing\TrustedRedirectResponse;
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
use Drupal\preprocess_event_dispatcher\Event\NodePreprocessEvent;
use Drupal\rabbit_hole\BehaviorInvokerInterface;
use Drupal\rabbit_hole\Plugin\RabbitHoleBehaviorPluginInterface;
use Drupal\rabbit_hole\Plugin\RabbitHoleBehaviorPluginManager;
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
   * Rabbit hole behavior invoker service.
   *
   * @var \Drupal\rabbit_hole\BehaviorInvokerInterface
   */
  protected $rabbitHoleBehavior;

  /**
   * Rabbit hole behavior plugin manager.
   *
   * @var \Drupal\rabbit_hole\Plugin\RabbitHoleBehaviorPluginManager
   */
  protected $rabbitHolePluginManager;

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return [
      EntityHookEvents::ENTITY_PRE_SAVE => 'onEntityPresave',
      EntityHookEvents::ENTITY_INSERT => 'onEntityInsert',
      EntityHookEvents::ENTITY_UPDATE => 'onEntityUpdate',
      EntityHookEvents::ENTITY_DELETE => 'onEntityDelete',
      'preprocess_node' => 'preprocessNode',
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
   * @param \Drupal\rabbit_hole\BehaviorInvokerInterface $rabbit_hole_behavior
   *   Rabbit hole behavior invoker service.
   * @param \Drupal\rabbit_hole\Plugin\RabbitHoleBehaviorPluginManager $rabbit_hole_plugin_manager
   *   Rabbit hole behavior plugin manager.
   */
  public function __construct(StanfordDefaultContentInterface $stanford_default_content, StateInterface $state, EntityTypeManagerInterface $entity_type_manager, BehaviorInvokerInterface $rabbit_hole_behavior, RabbitHoleBehaviorPluginManager $rabbit_hole_plugin_manager) {
    $this->defaultContent = $stanford_default_content;
    $this->state = $state;
    $this->entityTypeManager = $entity_type_manager;
    $this->rabbitHoleBehavior = $rabbit_hole_behavior;
    $this->rabbitHolePluginManager = $rabbit_hole_plugin_manager;
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
   * Before saving a new node, if it's the first one, create a list page.
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
   * Before saving a new node, if it's the first one, create a list page.
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
   * Before saving a new node, if it's the first one, create a list page.
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

  protected static function preSaveConfigPages(ConfigPagesInterface $config_page){
    if (
      $config_page->hasField('su_site_url') &&
      ($url_field = $config_page->get('su_site_url')->getValue())
    ) {
      // Set the xml sitemap module state to the new domain.
      \Drupal::state()->set('xmlsitemap_base_url', $url_field[0]['uri']);
    }

    // Invalidate cache tags on config pages save. This is a blanket cache clear
    // since config pages mostly affect the entire site.
    Cache::invalidateTags(['config:system.site', 'system.site', 'block_view', 'node_view']);
  }

  protected static function preSaveFieldStorageConfig(FieldStorageConfigInterface $field_storage){
    // If a field is saved and the field permissions are public, lets just remove
    // those third party settings before save so that it keeps the config clean.
    if ($field_storage->getThirdPartySetting('field_permissions', 'permission_type') === 'public') {
      $field_storage->unsetThirdPartySetting('field_permissions', 'permission_type');
      $field_storage->calculateDependencies();
    }
  }

  protected function insertMenuLinkContent(MenuLinkContentInterface $entity){
    Cache::invalidateTags(['stanford_profile_helper:menu_links']);
  }

  protected function deleteMenuLinkContent(MenuLinkContentInterface $entity){
    Cache::invalidateTags(['stanford_profile_helper:menu_links']);
  }

  protected function updateMenuLinkContent(MenuLinkContentInterface $entity, MenuLinkContentInterface $original_entity){
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

    // For new menu link items created on a node form (normally), set the expanded
    // attribute so all menu items are expanded by default.
    if ($entity->isNew()) {
      $entity->set('expanded', TRUE);
    }

    // When a menu item is added as a child of another menu item clear the parent
    // pages cache so that the block shows up as it doesn't get invalidated just
    // by the menu cache tags.
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
   *  Redirect to be saved.
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

  protected static function preSaveUserRole(RoleInterface $role){
    /** @var \Drupal\Core\Config\StorageInterface $config_storage */
    $config_storage = \Drupal::service('config.storage.sync');

    // Only modify new roles if they are created through the UI and don't exist in
    // the config management - Prefix them with "custm_" so they can be easily
    // identifiable.
    if (
      PHP_SAPI != 'cli' &&
      $role->isNew() &&
      !in_array($role->getConfigDependencyName(), $config_storage->listAll())
    ) {
      $role->set('id', 'custm_' . $role->id());
    }
  }

  /**
   * When preprocessing the node page, add the rabbit hole behavior message.
   *
   * @param \Drupal\preprocess_event_dispatcher\Event\NodePreprocessEvent $event
   *   Triggered Event.
   */
  public function preprocessNode(NodePreprocessEvent $event) {
    $node = $event->getVariables()->get('node');
    if ($event->getVariables()->get('page') && ($plugin = $this->getRabbitHolePlugin($node))) {
      $redirect_response = $plugin->performAction($node);

      // The action returned from the redirect plugin might be to show the
      // page. If it is, we don't want to display the message.
      if ($redirect_response instanceof TrustedRedirectResponse) {

        $content = $event->getVariables()->getByReference('content');
        $message = [
          '#theme' => 'rabbit_hole_message',
          '#destination' => $redirect_response->getTargetUrl(),
        ];
        $event->getVariables()
          ->set('content', ['rh_message' => $message] + $content);
      }
    }
  }

  /**
   * Get the rabbit hole behavior plugin for the given node.
   *
   * @param \Drupal\node\NodeInterface $entity
   *   Node with rabbit hole.
   *
   * @return \Drupal\rabbit_hole\Plugin\RabbitHoleBehaviorPluginInterface|null
   *   Redirect behavior plugin if applicable.
   */
  protected function getRabbitHolePlugin(NodeInterface $entity): ?RabbitHoleBehaviorPluginInterface {
    $values = $this->rabbitHoleBehavior->getRabbitHoleValuesForEntity($entity);
    if (isset($values['rh_action']) && $values['rh_action'] == 'page_redirect') {
      return $this->rabbitHolePluginManager->createInstance($values['rh_action'], $values);
    }
    return NULL;
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

      // Grab the node id from the internal path and use that as the destination.
      if (preg_match('/node\/(\d+)/', $path, $matches)) {
        return 'entity:node/' . $matches[1];
      }
    }
    return FALSE;
  }

}
