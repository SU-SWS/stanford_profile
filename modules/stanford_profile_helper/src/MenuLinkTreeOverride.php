<?php

namespace Drupal\stanford_profile_helper;

use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * Service decorator for the menu.link_tree service.
 */
class MenuLinkTreeOverride implements MenuLinkTreeInterface {

  /**
   * Original Menu Tree service.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuTree;

  /**
   * Menu Tree service override constructor.
   *
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menu_tree
   *   Original Menu Tree service.
   */
  public function __construct(MenuLinkTreeInterface $menu_tree) {
    $this->menuTree = $menu_tree;
  }

  /**
   * {@inheritDoc}
   */
  public function build(array $tree) {
    $build = $this->menuTree->build($tree);
    $build['#cache']['tags'][] = 'stanford_profile_helper:menu_links';
    // Remove node cache tags since we'll use our own cache tag above.
    StanfordProfileHelper::removeCacheTags($build, [
      '^node:*',
      '^config:system.menu.*',
    ]);
    return $build;
  }

  /**
   * {@inheritDoc}
   *
   * @codeCoverageIgnore
   */
  public function getCurrentRouteMenuTreeParameters($menu_name) {
    return $this->menuTree->getCurrentRouteMenuTreeParameters($menu_name);
  }

  /**
   * {@inheritDoc}
   */
  public function load($menu_name, MenuTreeParameters $parameters) {
    return $this->menuTree->load($menu_name, $parameters);
  }

  /**
   * {@inheritDoc}
   *
   * @codeCoverageIgnore
   */
  public function transform(array $tree, array $manipulators) {
    return $this->menuTree->transform($tree, $manipulators);
  }

  /**
   * {@inheritDoc}
   */
  public function maxDepth() {
    return $this->menuTree->maxDepth();
  }

  /**
   * {@inheritDoc}
   *
   * @codeCoverageIgnore
   */
  public function getSubtreeHeight($id) {
    return $this->menuTree->getSubtreeHeight($id);
  }

  /**
   * {@inheritDoc}
   *
   * @codeCoverageIgnore
   */
  public function getExpanded($menu_name, array $parents) {
    return $this->menuTree->getExpanded($menu_name, $parents);
  }

}
