<?php

namespace Drupal\stanford_profile_helper\EventSubscriber;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Installer\InstallerKernel;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityViewEvent;
use Drupal\node\NodeInterface;
use Drupal\rabbit_hole\BehaviorInvokerInterface;
use Drupal\stanford_profile_helper\StanfordDefaultContentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;

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
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return [
      EntityHookEvents::ENTITY_PRE_SAVE => 'onEntityPresave',
      EntityHookEvents::ENTITY_VIEW => 'onEntityView',
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
  public function __construct(StanfordDefaultContentInterface $stanford_default_content, StateInterface $state, EntityTypeManagerInterface $entity_type_manager, BehaviorInvokerInterface $rabbit_hole_behavior) {
    $this->defaultContent = $stanford_default_content;
    $this->state = $state;
    $this->entityTypeManager = $entity_type_manager;
    $this->rabbitHoleBehavior = $rabbit_hole_behavior;
  }

  /**
   * Before saving a new node, if it's the first one, create a list page.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent $event
   *   Triggered Event.
   */
  public function onEntityPresave(EntityPresaveEvent $event): void {
    if (
      InstallerKernel::installationAttempted() ||
      !($event->getEntity() instanceof NodeInterface) ||
      !$event->getEntity()->isNew()
    ) {
      return;
    }
    $pages = [
      'stanford_news' => '0b83d1e9-688a-4475-9673-a4c385f26247',
      'stanford_event' => '8ba98fcf-d390-4014-92de-c77a59b30f3b',
      'stanford_person' => '673a8fb8-39ac-49df-94c2-ed8d04db16a7',
      'stanford_course' => '14768832-f763-4d27-8df6-7cd784886d57',
    ];
    $bundle = $event->getEntity()->bundle();
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
        $entity = $this->defaultContent->createDefaultContent($pages[$bundle]);
        if ($entity) {
          $this->messenger()
            ->addMessage($this->t('A new page was created automatically for you. View the @link page to make changes.', [
              '@link' => Link::fromTextAndUrl($entity->label(), $entity->toUrl())
                ->toString(),
            ]));
        }
      }
    }
  }

  /**
   * @param EntityViewEvent $event
   * @return void
   */
  public function onEntityView(EntityViewEvent $event){
    if($event->getEntity() instanceof NodeInterface && node_is_page($event->getEntity())){

      if ($response = $this->rabbitHoleBehavior->processEntity($event->getEntity())) {
        dpm($response);
      }
    }
  }

}
