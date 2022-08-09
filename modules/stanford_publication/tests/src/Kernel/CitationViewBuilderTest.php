<?php

namespace Drupal\Tests\stanford_publication\Kernel;

use Drupal\Core\Render\RenderContext;
use Drupal\stanford_publication\Entity\Citation;

/**
 * Class CitationViewBuilderTest
 *
 * @group stanford_publication
 * @coversDefaultClass \Drupal\stanford_publication\CitationViewBuilder
 */
class CitationViewBuilderTest extends PublicationTestBase {

  /**
   * The Citation entity view builder will construct the bibliography.
   */
  public function testViewBuilder() {
    /** @var \Drupal\stanford_publication\Entity\CitationInterface $citation */
    $citation = Citation::create([
      'type' => 'su_book',
      'su_author' => [['given' => 'John', 'family' => 'Doe']],
      'su_year' => date('Y'),
      'su_edition' => 5,
      'su_page' => '10-20',
      'su_publisher' => 'Awesome Publishing',
      'su_publisher_place' => 'California',
      'su_subtitle' => 'subtitle of book',
    ]);
    $citation->setLabel('Foo Bar');
    $citation->save();

    $view_builder = \Drupal::entityTypeManager()
      ->getViewBuilder('citation');
    $build = $view_builder->view($citation);
    $build = $view_builder->build($build);

    $this->assertArrayNotHasKey('citation', $build);

    $build = $view_builder->view($citation, 'apa');
    $build['field_foo'] = [];
    $build = $view_builder->build($build);

    $this->assertArrayNotHasKey('field_foo', $build);
    $this->assertArrayHasKey('citation', $build);

  }

  /**
   * In the default display, the year, month and day fields get consolidated.
   */
  public function testDateDisplay() {
    /** @var \Drupal\stanford_publication\Entity\CitationInterface $citation */
    $citation = Citation::create([
      'title' => 'Foo Bar',
      'type' => 'su_article_journal',
      'su_year' => 2020,
      'su_month' => 5,
      'su_day' => 20,
    ]);
    $citation->save();

    $view_builder = \Drupal::entityTypeManager()
      ->getViewBuilder('citation');
    $build = $view_builder->view($citation);

    $context = new RenderContext();
    $build = \Drupal::service('renderer')
      ->executeInRenderContext($context, function () use ($view_builder, $build) {
        return $view_builder->build($build);
      });

    $this->assertEquals('May 20, 2020', $build['su_year'][0]['#markup']);

    $citation->set('su_month', NULL)->set('su_day', NULL)->save();
    $build = $view_builder->view($citation);
    $build = \Drupal::service('renderer')
      ->executeInRenderContext($context, function () use ($view_builder, $build) {
        return $view_builder->build($build);
      });
    $this->assertEquals('2020', $build['su_year'][0]['#markup']);
  }

  /**
   * Without a day value, the month and year should still display.
   */
  public function testDateDisplayNoDay() {
    /** @var \Drupal\stanford_publication\Entity\CitationInterface $citation */
    $citation = Citation::create([
      'title' => 'Foo Bar',
      'type' => 'su_article_journal',
      'su_year' => 2020,
      'su_month' => 5,
    ]);
    $citation->save();

    $view_builder = \Drupal::entityTypeManager()
      ->getViewBuilder('citation');
    $build = $view_builder->view($citation);

    $context = new RenderContext();
    $build = \Drupal::service('renderer')
      ->executeInRenderContext($context, function () use ($view_builder, $build) {
        return $view_builder->build($build);
      });

    $this->assertEquals('May, 2020', $build['su_year'][0]['#markup']);
  }

}
