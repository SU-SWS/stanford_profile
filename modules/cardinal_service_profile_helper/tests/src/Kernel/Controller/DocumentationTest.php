<?php

namespace Drupal\Tests\cardinal_service_profile_helper\Kernel\Controller;

use Drupal\cardinal_service_profile_helper\Controller\Documentation;
use Drupal\KernelTests\KernelTestBase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class DocumentationTest.
 *
 * @group cardinal_service_profile
 * @coversDefaultClass \Drupal\cardinal_service_profile_helper\Controller\Documentation
 */
class DocumentationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'cardinal_service_profile_helper',
  ];

  /**
   * Controller object.
   *
   * @var \Drupal\cardinal_service_profile_helper\Controller\Documentation
   */
  protected $controller;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->controller = new Documentation();
  }

  /**
   * Routes without a help document will return 404 response.
   */
  public function testPageNotFound() {
    $this->expectException(NotFoundHttpException::class);
    $this->controller->getDocumentation('foo-bar');
  }

  /**
   * Help pages will consist of some markup and a title.
   */
  public function testDocumentationController() {
    $this->assertCount(2, $this->controller->getDocumentation('opportunities'));
    $this->assertNotEmpty($this->controller->getTitle('opportunities'));
  }

}
