<?php

declare(strict_types=1);

namespace Drupal\stanford_basic\Hook;

use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbHooks {

  public function __construct(
    protected RequestStack $requestStack,
    protected RouteMatchInterface $routeMatch,
    protected TitleResolverInterface $titleResolver,
  ) {}

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_breadcrumb')]
  public function preprocessBreadcrumb(&$variables): void {
    $request = $this->requestStack->getCurrentRequest();
    if ($route_object = $this->routeMatch->getRouteObject()) {
      $page_title = $this->titleResolver->getTitle($request, $route_object);
      $variables['breadcrumb'][] = ['text' => $page_title];
    }
  }

}
