<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;

class BookHooks {

  /**
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_book_tree_alter')]
  public function themeSuggestionsBookTreeAlter(array &$suggestions, array $variables, $hook): void {
    $suggestions[] = $hook . '__secondary_nav';
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_book_tree')]
  public function preprocessBookTree(&$variables, $hook): void {
    $current_url = Url::fromRoute('<current>')->toString(TRUE)->getGeneratedUrl();
    $this->setActiveBookItem($variables['items'], $current_url);
  }

  /**
   * Dive into the book tree to set the current active item.
   *
   * @param array $items
   *   Book tree list.
   * @param string $current_url
   *   Current url path.
   */
  protected function setActiveBookItem(array &$items, string $current_url): void {
    foreach ($items as &$item) {
      if ($item['url']->toString(TRUE)->getGeneratedUrl() == $current_url) {
        $item['is_active'] = TRUE;
        return;
      }

      if (!empty($item['below'])) {
        $this->setActiveBookItem($item['below'], $current_url);
      }
    }
  }

}
