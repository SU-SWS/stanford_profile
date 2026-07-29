<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteObjectInterface;

class StanfordBasicParagraphHooks {

  /**
   * @codeCoverageIgnore
   */
  #[Hook('preprocess_paragraph__stanford_page_title_banner')]
  public function preprocessParagraphStanfordPageTitleBanner(&$variables) {
    $request = \Drupal::request();
    $route = $request->attributes->get(RouteObjectInterface::ROUTE_OBJECT);

    $page_title = $route ? \Drupal::service('title_resolver')
      ->getTitle($request, $route) : NULL;

    $variables['page_title'] = $page_title;
  }

  /**
   * Implements hook_preprocess_paragraph().
   */
  #[Hook('preprocess_paragraph')]
  public function preprocessParagraph(&$variables): void {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $variables['paragraph'];
    $ptype = Html::cleanCssIdentifier("ptype-" . $paragraph->bundle());
    $variables['attributes']['class'][] = $ptype;
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_paragraph__stanford_spacer')]
  public function preprocessParagraphStanfordSpacer(&$variables): void {
    $paragraph = $variables['elements']['#paragraph'];
    if ($paragraph->hasField('su_spacer_size') && !$paragraph->get('su_spacer_size')
        ->isEmpty()) {
      $variables['attributes']['class'][] = $paragraph->get('su_spacer_size')
        ->getString();
    }
  }

}
