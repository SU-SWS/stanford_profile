<?php

namespace Drupal\stanford_profile\Plugin\InstallTask;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteBuilderInterface;
use Drupal\stanford_profile\InstallTaskBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Rebuilds the routes.
 *
 * @InstallTask(
 *   id="stanford_profile_route_rebuilder"
 * )
 */
class RouteRebuilder extends InstallTaskBase implements ContainerFactoryPluginInterface {

  /**
   * Route builder service.
   *
   * @var \Drupal\Core\Routing\RouteBuilderInterface
   */
  protected $routeBuilder;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('router.builder')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteBuilderInterface $route_builder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeBuilder = $route_builder;
  }

  /**
   * {@inheritDoc}
   */
  public function runTask(array &$install_state) {
    $this->routeBuilder->rebuildIfNeeded();
    node_access_rebuild();
  }

}
