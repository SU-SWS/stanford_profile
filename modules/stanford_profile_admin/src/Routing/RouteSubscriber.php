<?php

namespace Drupal\stanford_profile_admin\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\stanford_profile_admin\Routing
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritDoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    foreach ($collection as $route) {
      if (strpos($route->getPath(), '/admin/people') === 0) {
        $route->setPath(str_replace('/admin/people', '/admin/users', $route->getPath()));
      }
    }
    if ($route = $collection->get('entity.user.collection')) {
      $route->setDefault('_title', 'Users');
    }
  }

}
