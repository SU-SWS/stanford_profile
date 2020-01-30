<?php

namespace Drupal\stanford_profile\Plugin\HelpSection;

use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Trait ProfileHelpTrait.
 *
 * @package Drupal\stanford_profile\Plugin\HelpSection
 */
trait ProfileHelpTrait {

  /**
   * Build and get a link string from the provided text and url.
   *
   * @param string|\Drupal\Core\StringTranslation\TranslatableMarkup $text
   *   Link text.
   * @param string $url
   *   Link url.
   * @param bool $button
   *   If the link should be a button.
   *
   * @return string
   *   The link HTML markup.
   */
  protected static function getLinkString($text, $url, $button = FALSE) {
    $attributes = [];
    if ($button) {
      $attributes['class'][] = 'button';
    }
    $url = Url::fromUri($url, ['attributes' => $attributes]);
    $link = Link::fromTextAndUrl($text, $url);
    return (string) $link->toString();
  }

}
