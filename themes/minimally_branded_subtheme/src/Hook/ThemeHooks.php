<?php

declare(strict_types=1);

namespace Drupal\minimally_branded_subtheme\Hook;

use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\Hook\Attribute\Hook;

class ThemeHooks {

  public function __construct(protected ThemeExtensionList $themeExtensionList) {}

  /**
   * Prepares variables for the html.html.twig template.
   */
  #[Hook('preprocess_html')]
  public function preprocessHtml(&$variables): void {
    $variables['stanford_basic_path'] = $this->themeExtensionList->getPath('stanford_basic');
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_block_alter')]
  public function themeSuggestionsBlockAlter(array &$suggestions, array $variables): void {
    if (!empty($variables['elements']['#id']) && $variables['elements']['#id'] == 'minimally_branded_subtheme_search') {
      $suggestions[] = 'block__stanford_basic_search';
    }
  }

  /**
   * Hide lockup wordmark.
   */
  #[Hook('preprocess_config_pages__stanford_local_footer')]
  function preprocessConfigPagesStanfordLocalFooter(&$variables) {
    $variables['hide_lockup_wordmark'] = TRUE;
  }

}
