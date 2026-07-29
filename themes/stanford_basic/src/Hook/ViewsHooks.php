<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\views\ViewExecutable;

class ViewsHooks {

  /**
   * Implements hook_preprocess_views_view().
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(&$variables): void {
    $variables['attributes']['class'][] = Html::cleanCssIdentifier('view');
    $variables['attributes']['class'][] = Html::cleanCssIdentifier($variables['id']);
    $variables['attributes']['class'][] = Html::cleanCssIdentifier($variables['display_id']);
    if ($variables['id'] == 'change_logs' && $variables['display_id'] == 'policy_changes') {
      $variables['#attached']['library'][] = 'stanford_basic/content.stanford_policy';
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_views_view_list_alter')]
  public function themeSuggestionsViewsViewListAlter(array &$suggestions, array $variables): void {
    /** @var \Drupal\views\ViewExecutable $view */
    $view = $variables['view'];
    $view_id = $view->id();
    $display_id = $view->current_display;
    $suggestions[] = "views_view_list__$view_id";
    $suggestions[] = "views_view_list__{$view_id}__$display_id";
  }

}
