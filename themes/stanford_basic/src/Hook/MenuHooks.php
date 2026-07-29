<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Component\Utility\Html;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Path\PathMatcherInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuHooks {

  use StringTranslationTrait;

  /**
   * Cached front-page path/alias pair, keyed to avoid repeat config lookups.
   *
   * @var array|null
   */
  protected ?array $frontPath = NULL;

  public function __construct(
    protected RequestStack $requestStack,
    protected ThemeSettingsProvider $themeSettingsProvider,
    protected PathMatcherInterface $pathMatcher,
    protected ConfigFactoryInterface $configFactory,
    protected AliasManagerInterface $aliasManager,
    protected EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Implements hook_preprocess_menu().
   */
  #[Hook('preprocess_menu')]
  public function preprocessMenu(&$variables, $hook): void {
    // This should run for every menu.
    $current_path = $this->requestStack->getCurrentRequest()->getRequestUri();
    $items = $variables['items'];
    foreach ($items as $key => $item) {
      // If path is current_path, add aria-current to the link.
      $item_path = parse_url($item['url']->toString(TRUE)->getGeneratedUrl(), PHP_URL_PATH);
      if ($item_path == $current_path) {
        $link_options = $variables['items'][$key]['url']->getOptions();
        $link_options['attributes']['aria-current'] = 'true';
        $variables['items'][$key]['url']->setOptions($link_options);
        $variables['items'][$key]['attributes']['aria-current'] = 'true';
      }
    }

    // The following code should only run for the main menu.
    if ($variables['menu_name'] !== "main") {
      return;
    }

    $this->menuProcessSubmenu($variables['items'], $current_path);
    if ($this->themeSettingsProvider->getSetting('nav_dropdown_enabled', 'stanford_basic')) {
      $variables['attributes']['class'][] = 'su-multi-menu--dropdowns';
    }
  }

  /**
   * Set active and active-trail class for sub-menus recursively.
   *
   * @param array $submenu
   *   The `$item['below']` structure from a menu array.
   * @param string $current_path
   *   A path to match against for "on this page".
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function menuProcessSubmenu(&$submenu, $current_path): void {
    $is_front = $this->pathMatcher->isFrontPage();

    foreach ($submenu as &$item) {
      $item_path = is_string($item['url']) ? $item['url'] : $item['url']->toString(TRUE)->getGeneratedUrl();
      if ($item_path == $current_path || ($this->pathIsHome($item_path) && $is_front)) {
        $item['is_active'] = TRUE;
      }

      if ($item['url'] instanceof Url && !$this->linkIsPublic($item['url'])) {
        $item['unpublished'] = TRUE;
      }

      if (!empty($item['below'])) {
        $this->menuProcessSubmenu($item['below'], $current_path);
      }
    }
  }

  /**
   * Determine if a menu path corresponds to the current home page.
   *
   * @param string $path
   *   The path given for a menu entry.
   *
   * @return bool
   *   TRUE if the path corresponds to the current home page.
   */
  protected function pathIsHome(string $path): bool {
    // Account for weird paths in input.
    $normal_path = strtolower(trim($path));

    if ($this->frontPath === NULL) {
      $config = $this->configFactory->get('system.site');
      $front_uri = $config->get('page.front');
      $front_alias = $this->aliasManager->getAliasByPath($front_uri);
      $this->frontPath = [$front_uri, $front_alias];
    }

    [$front_uri, $front_alias] = $this->frontPath;

    if (
      $normal_path == base_path() ||
      $normal_path == $front_uri ||
      $normal_path == $front_alias ||
      $normal_path == '<front>'
    ) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Is the given url public.
   *
   * @param \Drupal\Core\Url $url
   *   Menu link url object.
   *
   * @return bool
   *   False if the link is a node link to an unpublished link, true otherwise.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function linkIsPublic(Url $url): bool {
    if ($url->isRouted() && $url->getRouteName() == 'entity.node.canonical') {
      $parameters = $url->getRouteParameters();
      /** @var \Drupal\node\NodeInterface $node */
      $node = $this->entityTypeManager
        ->getStorage('node')
        ->load($parameters['node']);
      if ($node) {
        return $node->isPublished();
      }
      return TRUE;
    }
    return TRUE;
  }

  /**
   * Implements hook_preprocess_menu_local_task().
   */
  #[Hook('preprocess_menu_local_task')]
  public function preprocessMenuLocalTask(&$variables): void {
    if ($variables['link']['#url']->getRouteName() == 'entity.node.version_history') {
      $variables['link']['#title'] = $this->t('Version History');
    }
    $variables['attributes']['class'][] = strtolower(Html::cleanCssIdentifier($variables['link']['#title']));
    if ($variables['link']['#url']->getRouteName() == 'entity.node.canonical') {
      $variables['link']['#title'] = $this->t('Page Content');
      $variables['attributes']['class'][] = 'page-content-label';
    }
  }

}
