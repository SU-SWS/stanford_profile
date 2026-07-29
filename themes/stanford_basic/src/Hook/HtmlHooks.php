<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\path_alias\AliasManagerInterface;

class HtmlHooks {

  public function __construct(
    protected PathMatcherInterface $pathMatcher,
    protected CurrentPathStack $currentPathStack,
    protected AliasManagerInterface $aliasManager,
    protected ThemeManagerInterface $themeManager,
    protected ConfigFactoryInterface $configFactory,
    protected ModuleHandlerInterface $moduleHandler,
    protected ThemeSettingsProvider $themeSettingsProvider,
  ) {}

  /**
   * Prepares variables for the html.html.twig template.
   */
  #[Hook('preprocess_html')]
  public function preprocessHtml(&$variables): void {
    $favicon = $this->themeSettingsProvider->getSetting('favicon') ?: [];
    $variables['custom_favicon'] = isset($favicon['use_default']) && !$favicon['use_default'] && !empty($favicon['path']);

    try {
      $variables['is_front'] = $this->pathMatcher->isFrontPage();
    }
    catch (\Exception $e) {
      // If the database is not yet available, set default values for these
      // variables.
      $variables['is_front'] = FALSE;
    }

    // If we're on the front page.
    if (!$variables['is_front']) {
      // Add unique classes for each page and website section.
      $path = $this->currentPathStack->getPath();
      $alias = $this->aliasManager->getAliasByPath($path);
      $alias = trim($alias, '/');
      if (!empty($alias)) {
        $name = str_replace('/', '-', $alias);
        $variables['attributes']['class'][] = 'page-' . $name;
        [$section,] = explode('/', $alias, 2);
        if (!empty($section)) {
          $variables['attributes']['class'][] = 'section-' . $section;
        }
      }
    }

    // Add cachability metadata.
    $theme_name = $this->themeManager->getActiveTheme()->getName();
    $theme_settings_config = $this->configFactory->get($theme_name . '.settings');
    CacheableMetadata::createFromRenderArray($variables)
      ->addCacheableDependency($theme_settings_config)
      ->applyTo($variables);
    // Union all theme setting variables to the html.html.twig template.
    $variables += $theme_settings_config->getOriginal();

    // The base path.
    $variables['base_path'] = base_path();

    // Add global google analytics tracker if the site is on Acquia.
    if (isset($_ENV['AH_SITE_ENVIRONMENT'])) {
      $variables['add_global_ga'] = TRUE;
      if ($this->moduleHandler->moduleExists('google_analytics')) {
        $variables['ga_module_enabled'] = !empty($this->configFactory->get('google_analytics.settings')
          ->get('account'));
      }
    }
  }

}
