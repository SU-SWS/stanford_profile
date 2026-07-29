<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;

class FieldHooks {

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_field__su_stat_headline')]
  public function preprocessFieldSuStatHeadline(&$variables): void {
    $entity = $variables['element']['#object'];
    $heading_lvl = $entity->get('su_stat_headline_lvl')?->getString() ?: 'h2';
    if (!str_contains($heading_lvl, '.')) {
      $heading_lvl .= '.';
    }
    // Split the value like div.foo.bar into tag and classes. Then explode the
    // classes next to build an array.
    [$tag, $classes] = explode('.', $heading_lvl, 2);
    $variables['items'][0]['content']['#tag'] = $tag;
    $variables['items'][0]['content']['#attributes']['class'] = explode('.', $classes);

    if (!!$entity->get('su_stat_heading_hide')?->getString()) {
      $variables['items'][0]['content']['#attributes']['class'][] = 'visually-hidden';
    }
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_field__fontawesome_icon')]
  public function preprocessFieldFontawesomeIcon(&$variables): void {
    $variables['items'][0]['content']['#icons'][0]['#field_name'] = $variables['element']['#field_name'];
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_fontawesomeicons_alter')]
  public function themeSuggestionsFontawesomeiconsAlter(array &$suggestions, array &$variables): void {
    if (isset($variables['icons'][0]['#field_name'])) {
      $suggestions[] = 'fontawesomeicons__' . $variables['icons'][0]['#field_name'];
    }
  }

  /**
   * Prepares variables for the field.html.twig template.
   */
  #[Hook('preprocess_field')]
  public function preprocessField(&$variables, $hook): void {
    // Make additional variables available to the template.
    $variables['bundle'] = $variables['element']['#bundle'];
    $variables['attributes']['class'][] = Html::cleanCssIdentifier($variables['entity_type']);
    $variables['attributes']['class'][] = Html::cleanCssIdentifier($variables['bundle']);
    $variables['attributes']['class'][] = Html::cleanCssIdentifier($variables['field_name']);
    $variables['attributes']['class'][] = Html::cleanCssIdentifier($variables['field_type']);
    $variables['attributes']['class'][] = Html::cleanCssIdentifier('label-' . $variables['label_display']);

    $first_item = isset($variables['element'][0]) ? $variables['element'][0] : NULL;
    $is_paragraph = isset($first_item['#paragraph']) ? $first_item['#paragraph'] : FALSE;
    $has_items = isset($variables['items']) ? count($variables['items']) : FALSE;

    // Add additional information to paragraph fields.
    // Bricks has a different field type and structures the array differently, so
    // we need to check if its actual normal paragraph fields as well.
    if ($variables['field_type'] == 'entity_reference_revisions' && $is_paragraph && $has_items) {
      foreach ($variables['items'] as &$pitem) {
        $paragraph_type = $pitem['content']['#paragraph']->getType();
        $ptype = Html::cleanCssIdentifier("ptype-" . $paragraph_type);
        if (!isset($pitem['attributes']['class'])) {
          $pitem['attributes']['class'] = [];
        }
        $pitem['attributes']['class'][] = "paragraph-item";
        $pitem['attributes']['class'][] = $ptype;
      }
    }
  }

}
