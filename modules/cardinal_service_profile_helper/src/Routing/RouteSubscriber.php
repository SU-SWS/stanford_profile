<?php

namespace Drupal\cardinal_service_profile_helper\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\cardinal_service_profile\Routing
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritDoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('csv_importer.form')) {
      // Move the CSV importer to an easier location and change its permission.
      $route->setPath('/admin/content/import');
      $route->setRequirement('_permission', 'administer nodes');
    }
  }

}
