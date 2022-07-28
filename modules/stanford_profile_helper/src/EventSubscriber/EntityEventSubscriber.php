<?php

namespace Drupal\stanford_profile_helper\EventSubscriber;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Installer\InstallerKernel;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Drupal\menu_link_content\MenuLinkContentInterface;
use Drupal\node\NodeInterface;
use Drupal\preprocess_event_dispatcher\Event\NodePreprocessEvent;
use Drupal\rabbit_hole\BehaviorInvokerInterface;
use Drupal\rabbit_hole\Plugin\RabbitHoleBehaviorPluginInterface;
use Drupal\rabbit_hole\Plugin\RabbitHoleBehaviorPluginManager;
use Drupal\stanford_profile_helper\StanfordDefaultContentInterface;
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
    $entity_type = $event->getEntity()->getEntityTypeId();
    $method_name = 'preSave' . str_replace(' ', '', ucwords(str_replace('_', ' ', $entity_type)));
    // Call individual methods for each entity type if one is available.
    if (method_exists($this, $method_name)) {
      $this->{$method_name}($event->getEntity());
    }
  }

  /**
   * Before saving a menu item, adjust the path if an internal path exists.
   *
   * @param \Drupal\menu_link_content\MenuLinkContentInterface $entity
   *   The menu link being saved.
   */
  protected function preSaveMenuLinkContent(MenuLinkContentInterface $entity): void {
    $destination = $entity->get('link')->getString();
    if ($internal_path = $this->lookupInternalPath($destination)) {
      $entity->set('link', $internal_path);
    }
  }

  /**
   * Before saving a redirect, adjust the path if an internal path exists.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *  Redirect to be saved.
   */
  protected function preSaveRedirect(ContentEntityInterface $entity): void {
    $destination = $entity->get('redirect_redirect')->getString();
    if ($internal_path = $this->lookupInternalPath($destination)) {
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
