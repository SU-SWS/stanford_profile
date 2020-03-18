<?php
namespace Drupal\stanford_profile_permissions\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Add permission to '/admin/flush'.
    if ($route = $collection->get('admin_toolbar_tools.flush')) {
      $route->setRequirement('_permission', 'administer site configuration+flush caches');
    }
  }

}
