<?php

namespace Drupal\stanford_policy\EventSubscriber;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Stanford Policy event subscriber.
 */
class StanfordPolicySubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      EntityHookEvents::ENTITY_PRE_SAVE => 'onEntityPresave',
    ];
  }

  /**
   * Act on policy node being saved.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent $event
   *   Triggered Event.
   */
  public function onEntityPresave(EntityPresaveEvent $event): void {
    $entity = $event->getEntity();

    if ($entity->getEntityTypeId() == 'node' && $entity->bundle() == 'stanford_policy') {
      /** @var \Drupal\node\NodeInterface $entity */
      $prefix = [
        $entity->get('su_policy_chapter')->getString(),
        $entity->get('su_policy_subchapter')->getString(),
        $entity->get('su_policy_policy_num')->getString(),
      ];
      $title = implode('.', array_filter($prefix)) . ' ' . $entity->label();
      $entity->set('title', trim($title));
    }
  }

}
