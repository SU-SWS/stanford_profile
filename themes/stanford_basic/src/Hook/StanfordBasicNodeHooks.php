<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\PathMatcherInterface;

/**
 * @codeCoverageIgnore
 */
class StanfordBasicNodeHooks {

  public function __construct(protected PathMatcherInterface $pathMatcher) {}

  #[Hook('preprocess_node')]
  public function preprocessNode(&$variables) {
    $node_bundle = $variables['node']->bundle();
    $variables['#attached']['library'][] = 'stanford_basic/content.' . $node_bundle;
    unset($variables['content']['book_navigation']);

    $variables['attributes']['class'][] = Html::cleanCssIdentifier('node--' . $node_bundle);

    if ($variables['view_mode'] == 'full') {
      $variables['attributes']['class'][] = Html::cleanCssIdentifier('node-page--' . $node_bundle);
    }
    else {
      // Backwards compatibility with UI patterns.
      $variables['attributes']['class'][] = Html::cleanCssIdentifier('ds-entity--node');
      $variables['attributes']['class'][] = Html::cleanCssIdentifier('ds-entity--' . $node_bundle);
    }
  }

  #[Hook('preprocess_node__stanford_page')]
  public function preprocessPage(&$variables) {
    if (
      $variables['view_mode'] == 'full' &&
      $this->pathMatcher->isFrontPage()
    ) {
      /** @var \Drupal\node\NodeInterface $node */
      $node = $variables['node'];
      if (
        !$node->get('su_page_banner')->count() &&
        $node->get('su_page_components')->count() >= 2 &&
        $node->get('su_page_components')
          ->get(1)->entity->bundle() == 'stanford_wysiwyg'
      ) {
        $variables['attributes']['class'][] = 'add-more-space-to-top';
      }
    }
  }

  #[Hook('preprocess_node__stanford_news')]
  public function preprocessNews(&$variables) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $variables['node'];
  }

  #[Hook('preprocess_field__su_news_featured_media')]
  public function preprocessNewsImageField(&$variables) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $variables['element']['#object'];

    if ($node->get('layout_selection')->getString() == 'news_spotlight') {
      $variables['items'][0]['content']['#stanford_media_image_style'] = 'square_1192';
    }
  }

}
