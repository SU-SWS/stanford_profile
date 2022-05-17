<?php

namespace Drupal\stanford_profile_helper\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Installer\InstallerKernel;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\State\StateInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Drupal\node\NodeInterface;
use Drupal\stanford_profile_helper\StanfordDefaultContentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityEventSubscriber implements EventSubscriberInterface {

  use MessengerTrait;

  protected $defaultContent;

  protected $state;

  protected $entityTypeManager;

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    return [
      EntityHookEvents::ENTITY_PRE_SAVE => 'onEntityPresave',
    ];
  }

  public function __construct(StanfordDefaultContentInterface $stanford_default_content, StateInterface $state, EntityTypeManagerInterface $entity_type_manager) {
    $this->defaultContent = $stanford_default_content;
    $this->state = $state;
    $this->entityTypeManager = $entity_type_manager;
  }

  public function onEntityPresave(EntityPresaveEvent $event) {
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
        $entity = $this->defaultContent->createDefaultListPage($pages[$bundle]);
        if ($entity) {
          $this->messenger()
            ->addMessage($this->t('A new page was created automatically for you. View the @link to make changes.', [
              '@link' => Link::fromTextAndUrl($entity->label(), $entity->toUrl())
                ->toString(),
            ]));
        }
      }
    }
  }

}
