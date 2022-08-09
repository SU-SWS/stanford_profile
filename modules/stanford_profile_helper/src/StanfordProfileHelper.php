<?php

namespace Drupal\stanford_profile_helper;

use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Module helper methods and service.
 */
class StanfordProfileHelper implements TrustedCallbackInterface {

  /**
   * Remove some cache tags from a render array.
   *
   * @param array|mixed $item
   *   Render array.
   * @param array $tags
   *   Cache tags to be removed from the render array using regex.
   */
  public static function removeCacheTags(&$item, array $tags = []) {
    if (!is_array($item) || empty($item['#cache']['tags'])) {
      return;
    }
    $item['#cache']['tags'] = array_filter($item['#cache']['tags'], function ($tag) use ($tags) {
      foreach ($tags as $search_tag) {
        if (preg_match("/$search_tag/", $tag)) {
          return FALSE;
        }
      }
      return TRUE;
    });
    $item['#cache']['tags'] = array_values($item['#cache']['tags']);
  }

  /**
   * {@inheritDoc}
   */
  public static function trustedCallbacks(): array {
    return ['preRenderDsEntity'];
  }

  /**
   * PreRender the ds entity to add contextual links.
   *
   * @param array $element
   *   Entity render array.
   *
   * @return array
   *   Altered render array.
   */
  public static function preRenderDsEntity(array $element): array {
    $module_handler = \Drupal::moduleHandler();
    if (isset($element['#contextual_links']) && $module_handler->moduleExists('contextual')) {
      $placeholder = [
        '#type' => 'contextual_links_placeholder',
        '#id' => _contextual_links_to_id($element['#contextual_links']),
      ];
      $element['#prefix'] = \Drupal::service('renderer')->render($placeholder);
    }
    return $element;
  }

}
