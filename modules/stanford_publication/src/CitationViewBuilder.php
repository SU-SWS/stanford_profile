<?php

namespace Drupal\stanford_publication;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Theme\Registry;
use Drupal\stanford_publication\Entity\CitationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CitationViewBuilder.
 *
 * @package Drupal\stanford_publication
 */
class CitationViewBuilder extends EntityViewBuilder {

  /**
   * Rendering service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * {@inheritDoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.repository'),
      $container->get('language_manager'),
      $container->get('theme.registry'),
      $container->get('entity_display.repository'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(EntityTypeInterface $entity_type, EntityRepositoryInterface $entity_repository, LanguageManagerInterface $language_manager, Registry $theme_registry, EntityDisplayRepositoryInterface $entity_display_repository, RendererInterface $renderer) {
    parent::__construct($entity_type, $entity_repository, $language_manager, $theme_registry, $entity_display_repository);
    $this->renderer = $renderer;
  }

  /**
   * {@inheritDoc}
   */
  public function build(array $build) {
    // This assumes the view mode name matches the constant name in the
    // interface.
    $constant = CitationInterface::class . '::' . strtoupper($build['#view_mode']);

    if (defined($constant) && $style = constant($constant)) {
      // Remove any field that is configured to display on the build so that
      // we can force the markup to be structured exactly how we need it based
      // on the citation styles.
      foreach (Element::children($build) as $child) {
        unset($build[$child]);
      }
      $build['citation']['#markup'] = $build['#citation']->getBibliography($style);
      return $build;
    }

    $build = parent::build($build);
    return $this->buildDateDisplay($build);
  }

  /**
   * Consolidate the year, month and day fields into a single string.
   *
   * @param array $build
   *   Render array.
   *
   * @return array
   *   Modified render array
   */
  protected function buildDateDisplay(array $build): array {
    $month = $build['su_month'] ?? [];
    $day = $build['su_day'] ?? [];
    unset($build['su_month'], $build['su_day']);

    // Publications need a year if they have a month and/or day, so if no year
    // is available, force the month and day to be removed.
    if (empty($build['su_year'][0]['#markup'])) {
      return $build;
    }

    // Copy the year over so that it has its own unique keys.
    $build['su_year']['#title'] = $this->t('Publication Date');

    $month['#label_display'] = 'hidden';
    $day['#label_display'] = 'hidden';
    $month = (int) trim(strip_tags($this->renderer->render($month)));
    $day = (int) trim(strip_tags($this->renderer->render($day)));

    if ($month) {
      $date = date('F', strtotime("1-$month-2000"));

      if ($day) {
        $date .= ' ' . $day;
      }
      $build['su_year'][0]['#markup'] = trim("$date, " . $build['su_year'][0]['#markup'], ', ');
    }

    return $build;
  }

}
