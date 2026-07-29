<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Extension\ThemeSettingsProvider;

class BlockHooks {

  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected MenuLinkTreeInterface $menuTree,
    protected ThemeSettingsProvider $themeSettingsProvider,
  ) {}

  /**
   * Implements hook_preprocess_block().
   */
  #[Hook('preprocess_block')]
  public function preprocessBlock(&$variables): void {
    $variables['attributes']['class'][] = Html::cleanCssIdentifier($this->changeCharacters($variables['base_plugin_id']));
    $variables['attributes']['class'][] = Html::cleanCssIdentifier($this->changeCharacters($variables['derivative_plugin_id']));

    if ($variables['plugin_id'] == 'system_menu_block:main' && \Drupal::hasService('config_pages.loader')) {
      /** @var \Drupal\config_pages\ConfigPagesLoaderServiceInterface $config_pages */
      $config_pages = \Drupal::service('config_pages.loader');
      $new_menu = (bool) $config_pages->getValue('stanford_basic_site_settings', 'su_site_new_menu', 0, 'value');
      if ($new_menu) {
        $variables['attributes']['data-island'] = 'main-menu-island';
        $variables['#attached']['library'][] = 'stanford_basic/decoupled_menu';
        $menu = $this->menuTree->load('main', new MenuTreeParameters());
        $variables['#attached']['drupalSettings']['stanford_basic']['decoupledMenuItems'] = $this->getMenuTreeLinks($menu);
        $variables['elements']['#cache']['tags'][] = 'stanford_profile_helper:menu_links';
      }
    }

    if ($variables['plugin_id'] == 'views_exposed_filter_block:search-results') {
      if (AlgoliaHooks::getAlgoliaSettings()) {
        unset($variables['content']);
      }
    }

    if (
      !isset($variables['configuration']['label_display']) ||
      $variables['configuration']['label_display'] != BlockPluginInterface::BLOCK_LABEL_VISIBLE
    ) {
      $variables['title_attributes']['aria-hidden'] = 'true';
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK_alter().
   */
  #[Hook('theme_suggestions_block_alter')]
  public function themeSuggestionsBlockAlter(array &$suggestions, array $variables): void {
    if ($variables['elements']['#base_plugin_id'] == 'system_menu_block') {
      if ($this->entityTypeManager->hasDefinition('taxonomy_menu')) {
        $taxonomy_menu = $this->entityTypeManager
          ->getStorage('taxonomy_menu')
          ->loadByProperties(['menu' => $variables['elements']['#derivative_plugin_id']]);
        if (!empty($taxonomy_menu)) {
          $suggestions[] = 'block__system_menu_block__filter_by';
        }
      }
    }
  }

  /**
   * Pass through the lockup configuration settings.
   */
  #[Hook('preprocess_block__system_branding_block')]
  public function preprocessBlockSystemBrandingBlock(&$vars): void {
    $vars['use_logo'] = $this->themeSettingsProvider->getSetting('logo.use_default');
    $vars['lockup'] = $this->themeSettingsProvider->getSetting('lockup') ?: ['option' => 'a'];
  }

  /**
   * Using a nested menu tree, recursively build a list of all menu links.
   *
   * @param \Drupal\Core\Menu\MenuLinkTreeElement[] $items
   *   Menu tree.
   */
  protected function getMenuTreeLinks(array $items): array {
    $tree = [];
    foreach ($items as $id => $item) {
      if (!$item->link->isEnabled() || !$item->link->getUrlObject()->access()) {
        continue;
      }
      $tree[] = [
        'id' => $id,
        'url' => $item->link->getUrlObject()->toString(),
        'title' => $item->link->getTitle(),
        'expanded' => $item->link->isExpanded(),
        'parent' => $item->link->getParent(),
        'weight' => $item->link->getWeight(),
        'items' => $this->getMenuTreeLinks($item->subtree),
      ];
    }
    uasort($tree, fn($a, $b) => $a['weight'] != $b['weight'] ? $a['weight'] <=> $b['weight'] : $a['title'] <=> $b['title']);
    return array_values($tree);
  }

  /**
   * Html::cleanCssIdentifier() doesn't remove ":" so we have to clean a little
   * more.
   *
   * @param string $string
   *   String to clean.
   *
   * @return string
   *   Cleaned string.
   */
  protected function changeCharacters($string): string {
    return $string ? preg_replace("/[^a-zA-Z\d\s]/", '-', $string) : '';
  }

}
