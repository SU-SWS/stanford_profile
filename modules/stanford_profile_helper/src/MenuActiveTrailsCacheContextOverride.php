<?php

namespace Drupal\stanford_profile_helper;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CalculatedCacheContextInterface;
use Drupal\Core\Cache\Context\MenuActiveTrailsCacheContext;

/**
 * Service decorator for core `cache_context.route.menu_active_trails` service.
 */
class MenuActiveTrailsCacheContextOverride implements CalculatedCacheContextInterface {

  /**
   * Original service.
   *
   * @var \Drupal\Core\Cache\Context\CalculatedCacheContextInterface
   */
  protected $cacheContext;

  /**
   * {@inheritDoc}
   */
  public static function getLabel() {
    return MenuActiveTrailsCacheContext::getLabel();
  }

  /**
   * Service decorator constructor.
   *
   * @param \Drupal\Core\Cache\Context\CalculatedCacheContextInterface $cache_context
   *   Original service.
   */
  public function __construct(CalculatedCacheContextInterface $cache_context) {
    $this->cacheContext = $cache_context;
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheableMetadata($parameter = NULL) {
    // Remove the cache tags from the original service.
    //
    // @see Drupal\Core\Cache\Context\MenuActiveTrailsCacheContext::getCacheableMetadata()
    return new CacheableMetadata();
  }

  /**
   * {@inheritDoc}
   */
  public function getContext($parameter = NULL) {
    return $this->cacheContext->getContext($parameter);
  }

}
