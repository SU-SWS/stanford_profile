<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\layout_builder\Plugin\SectionStorage\DefaultsSectionStorage;

class PageHooks {

  public function __construct(
    protected ThemeSettingsProvider $themeSettingsProvider,
    protected RouteMatchInterface $routeMatch,
    protected ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Implements hook_preprocess_page().
   */
  #[Hook('preprocess_page')]
  public function preprocessPage(&$vars): void {
    // Variant setting for the brand bar.
    $bbv = $this->themeSettingsProvider->getSetting('brand_bar_variant');
    if ($bbv == 'bright') {
      $bbv = 'default';
    }
    if ($bbv !== "none") {
      $vars['brand_bar_variant'] = 'su-brand-bar--' . $bbv;
    }
    // Variant setting for the global footer.
    if ($global_footer_variant = $this->themeSettingsProvider->getSetting('global_footer_variant')) {
      $vars['global_footer_variant'] = 'su-global-footer--' . $global_footer_variant;
    }

    // Prepares non node/panel pages to be in a single column.
    $this->preprocessNotNodes($vars);
  }

  /**
   * Only center the container if the page is not a node and not a panel.
   *
   * @param array $vars
   *   The page vars.
   */
  protected function preprocessNotNodes(&$vars): void {
    $pbag = $this->routeMatch->getParameters();
    $pkeys = $pbag->keys();
    $block_list = [
      'node', // Node Types.
      'page_manager_page', // Panels.
    ];

    foreach ($block_list as $key) {
      if (in_array($key, $pkeys)) {
        return;
      }
    }

    // Do not center when using the layout builder ui.
    if (
      in_array('section_storage', $pkeys) &&
      $pbag->get('section_storage') instanceof DefaultsSectionStorage
    ) {
      return;
    }

    foreach ($vars['page']['content'] as $key => &$block) {
      // Skip any non block config.
      if (strpos($key, "#") === 0) {
        continue;
      }
      // Ensure that the item is a block.
      if (isset($block['#block'])) {
        $block['#attributes']['class'][] = 'centered-container';
      }
    }
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_page__front')]
  public function preprocessPageFront(&$variables): void {
    $variables['site_name'] = $this->configFactory->get('system.site')->get('name');
  }

}
