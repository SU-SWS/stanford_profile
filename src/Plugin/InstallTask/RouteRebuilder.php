<?php

namespace Drupal\stanford_profile\Plugin\InstallTask;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\node\NodeAccessRebuild;
use Drupal\stanford_profile\Attribute\InstallTask;
use Drupal\stanford_profile\InstallTaskBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Rebuilds the routes.
 */
#[InstallTask(id: 'stanford_profile_route_rebuilder')]
class RouteRebuilder extends InstallTaskBase implements ContainerFactoryPluginInterface {

  /**
   * Route builder service.
   *
   * @var \Drupal\Core\Routing\RouteBuilderInterface
   */
  protected $routeBuilder;

  /**
   * Node access rebuild service.
   *
   * @var \Drupal\node\NodeAccessRebuild
   */
  protected $nodeAccessRebuild;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('router.builder'),
      $container->get(NodeAccessRebuild::class)
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteBuilderInterface $route_builder, NodeAccessRebuild $node_access_rebuild) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeBuilder = $route_builder;
    $this->nodeAccessRebuild = $node_access_rebuild;
  }

  /**
   * {@inheritDoc}
   */
  public function runTask(array &$install_state) {
    $this->routeBuilder->rebuildIfNeeded();
    $this->nodeAccessRebuild->rebuild();
  }

}
