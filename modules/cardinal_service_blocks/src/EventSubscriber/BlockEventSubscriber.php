<?php

namespace Drupal\cardinal_service_blocks\EventSubscriber;

use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Block event subscriber.
 *
 * @package Drupal\cardinal_service_blocks\EventSubscriber
 */
class BlockEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritDoc}
   */
  public static function getSubscribedEvents() {
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = [
      'onBuildRender',
      1,
    ];
    return $events;
  }

  /**
   * Alter the build of the block in the layout builder before rendering.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   Subscribed event.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event) {
    $component = $event->getComponent();
    // Action Urls are only set on views exposed filter blocks.
    // @see cardinal_service_blocks_form_layout_builder_configure_block_alter().
    if ($url = $component->getThirdPartySetting('cardinal_service_blocks', 'action_url')) {
      $build = $event->getBuild();
      $build['#configuration']['action_url'] = $url;
      $event->setBuild($build);
    }
  }

}
