<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteObjectInterface;

class StanfordBasicParagraphHooks {

  #[Hook('preprocess_paragraph__stanford_banner')]
  public function preprocessParagraphStanfordBanner(&$variables) {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $variables['paragraph'];

    $header_behavior = $paragraph->getBehaviorSetting('hero_pattern', 'heading', 'h2');
    preg_match('/^(\w+)(.*)$/', $header_behavior, $header_tag);

    $variables['component'] = [
      'header_tag' => $header_tag[1],
      'header_classes' => isset($header_tag[2]) ? trim(str_replace('.', ' ', $header_tag[2])) : NULL,
      'visually_hide_header' => (bool) $paragraph->getBehaviorSetting('hero_pattern', 'hide_heading', FALSE),
      'overlay_position' => $paragraph->getBehaviorSetting('hero_pattern', 'overlay_position', 'left'),
      'wrapper_class' => 'bottom-margin-' . $paragraph->getBehaviorSetting('hero_pattern', 'space_below', 'default'),
    ];
  }

  #[Hook('preprocess_paragraph__stanford_card')]
  public function preprocessParagraphStanfordCard(&$variables) {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $variables['paragraph'];

    $header_behavior = $paragraph->getBehaviorSetting('su_card_styles', 'heading', 'h2');
    preg_match('/^(\w+)(.*)$/', $header_behavior, $header_tag);
    $link_style = $paragraph->getBehaviorSetting('su_card_styles', 'link_style', 'button');
    $variables['component'] = [
      'header_tag' => $header_tag[1],
      'header_classes' => isset($header_tag[2]) ? trim(str_replace('.', ' ', $header_tag[2])) : NULL,
      'visually_hide_header' => (bool) $paragraph->getBehaviorSetting('su_card_styles', 'hide_heading', FALSE),
      'link_style' => $link_style,
    ];
  }

  #[Hook('preprocess_paragraph__stanford_page_title_banner')]
  public function preprocessParagraphStanfordPageTitleBanner(&$variables) {
    $request = \Drupal::request();
    $route = $request->attributes->get(RouteObjectInterface::ROUTE_OBJECT);

    $page_title = $route ? \Drupal::service('title_resolver')
      ->getTitle($request, $route) : NULL;

    $variables['page_title'] = $page_title;
  }

  #[Hook('preprocess_paragraph__stanford_stat_card')]
  public function preprocessParagraphStanfordStatCard(&$variables) {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $variables['paragraph'];
    $header_behavior = $paragraph->get('su_stat_headline_lvl')
      ?->getString() ?? 'h2';
    preg_match('/^(\w+)(.*)$/', $header_behavior, $header_tag);
    $bg_color = $paragraph->get('su_stat_bg_color')?->getString();
    $icon_color = $paragraph->get('su_stat_icon_color')?->getString();
    $stat_color = $paragraph->get('su_stat_stat_color')?->getString();
    $centered_text = (bool) $paragraph->get('su_stat_centered')?->getString();
    $variables['component'] = [
      'bg_color' => $bg_color,
      'icon_color' => $icon_color,
      'stat_color' => $stat_color,
      'centered_text' => $centered_text,
    ];
  }

}
