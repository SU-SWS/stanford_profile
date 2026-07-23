<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\PathMatcherInterface;

class StanfordBasicNodeHooks {

  public function __construct(protected PathMatcherInterface $pathMatcher) {}

  #[Hook('preprocess_node')]
  public function preprocessNode(&$variables) {
    $node_bundle = $variables['node']->bundle();
    $variables['#attached']['library'][] = 'stanford_basic/content.' . $node_bundle;
    unset($variables['content']['book_navigation']);

    if ($variables['view_mode'] == 'full') {
      $variables['attributes']['class'][] = Html::cleanCssIdentifier('node-page--' . $node_bundle);
    }
    else {
      // Backwards compatibility with UI patterns.
      $variables['attributes']['class'][] = Html::cleanCssIdentifier('ds-entity--node');
      $variables['attributes']['class'][] = Html::cleanCssIdentifier('ds-entity--' . $node_bundle);
      $variables['attributes']['class'][] = Html::cleanCssIdentifier('node--' . $node_bundle);
    }
  }

  #[Hook('preprocess_node__stanford_event')]
  public function preprocessEvent(&$variables) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $variables['node'];
    $date = $node->get('su_event_date_time')->get(0)?->getValue();
    $startMonth = $startDay = $endMonth = $endDay = NULL;
    if ($date) {
      [
        $startMonth,
        $startDay,
      ] = explode(' ', date('M j', (int) $date['value']));
      [
        $endMonth,
        $endDay,
      ] = explode(' ', date('M j', (int) $date['end_value']));
    }
    $variables['component'] = [
      'start_month' => $startMonth,
      'start_date' => $startDay,
      'end_month' => $endMonth,
      'end_date' => $endDay,
    ];
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

  #[Hook('preprocess_node__stanford_publication')]
  public function preprocessPublication(&$variables) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $variables['node'];
    $citation_type = $node->get('su_publication_citation')
      ->get(0)?->entity?->getBundleEntity()->label();
    $variables['component']['super_headline'] = $citation_type ?? 'Publication';
  }

}
