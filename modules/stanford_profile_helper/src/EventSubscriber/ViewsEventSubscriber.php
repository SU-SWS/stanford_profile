<?php

namespace Drupal\stanford_profile_helper\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\views_event_dispatcher\Event\Views\ViewsPreViewEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsQueryAlterEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewsEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[ViewsHookEvents::VIEWS_PRE_VIEW] = 'viewsPreView';
    return $events;
  }

  /**
   * Modify the cache tags on views.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPreViewEvent $event
   *   Pre view hook event.
   */
  public function viewsPreView(ViewsPreViewEvent $event) {
    $view = $event->getView();
    $display_options = &$view->getDisplay()->options;

    $default_options = &$view->getDisplay()->default_display->options;
    $filters = !empty($display_options['filters']) ? $display_options['filters'] : $default_options['filters'];

    // Change the default cache mechanism to use custom tags that we generate
    // using the node type filters that exist on the view.
    // @see \Drupal\Core\Entity\EntityBase::getListCacheTagsToInvalidate().
    if (!empty($filters['type']['entity_type']) && $filters['type']['entity_type'] == 'node') {
      $tags = [];
      foreach ($filters['type']['value'] as $node_type) {
        $tags[] = 'node_list:' . $node_type;
      }
      // If no node type tags are available, fall back to the general `node_list`.
      $tags = empty($tags) ? ['node_list'] : $tags;
      $cache = [
        'type' => 'custom_tag',
        'options' => ['custom_tag' => implode(' ', $tags)],
      ];
      $display_options['cache'] = $cache;
      $default_options['cache'] = $cache;
    }
  }

}
