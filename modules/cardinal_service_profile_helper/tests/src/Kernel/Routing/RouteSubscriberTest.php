<?php

namespace Drupal\Tests\cardinal_service_profile_helper\Kernel\Routing;

use Drupal\Core\Url;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class RouteSubscriberTest
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_profile_helper\Routing\RouteSubscriber
 */
class RouteSubscriberTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'csv_importer',
    'system',
    'cardinal_service_profile_helper',
  ];

  /**
   * The routes should be modified.
   */
  public function testRouteSubscriber() {
    $url = Url::fromRoute('csv_importer.form');
    $this->assertEqual('admin/content/import', $url->getInternalPath());
  }

}
