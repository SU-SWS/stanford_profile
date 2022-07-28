<?php

namespace Drupal\Tests\stanford_profile_helper\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\stanford_profile_helper\MenuActiveTrailsCacheContextOverride;

/**
 * Class MenuActiveTrailsCacheContextOverrideTest.
 *
 * @group stanford_profile_helper
 * @coversDefaultClass \Drupal\stanford_profile_helper\MenuActiveTrailsCacheContextOverride
 */
class MenuActiveTrailsCacheContextOverrideTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'stanford_profile_helper',
    'config_pages',
    'rabbit_hole',
  ];

  /**
   * Service decorator should override main service.
   */
  public function testMenuTrails() {
    $trail_service = \Drupal::service('cache_context.route.menu_active_trails');
    $this->assertInstanceOf(MenuActiveTrailsCacheContextOverride::class, $trail_service);
    $metadata = $trail_service->getCacheableMetadata('main_menu');
    $this->assertEmpty($metadata->getCacheTags());

    $context = $trail_service->getContext('main_menu');
    $this->assertNotEmpty($context);

    $this->assertNotEmpty(MenuActiveTrailsCacheContextOverride::getLabel());
  }

}
