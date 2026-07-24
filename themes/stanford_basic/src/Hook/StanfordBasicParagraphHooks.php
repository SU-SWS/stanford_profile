<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteObjectInterface;

/**
 * @codeCoverageIgnore
 */
class StanfordBasicParagraphHooks {

  #[Hook('preprocess_paragraph__stanford_page_title_banner')]
  public function preprocessParagraphStanfordPageTitleBanner(&$variables) {
    $request = \Drupal::request();
    $route = $request->attributes->get(RouteObjectInterface::ROUTE_OBJECT);

    $page_title = $route ? \Drupal::service('title_resolver')
      ->getTitle($request, $route) : NULL;

    $variables['page_title'] = $page_title;
  }

}
