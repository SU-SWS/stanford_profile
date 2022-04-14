<?php

namespace Drupal\stanford_publication;

use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\Core\Render\Element;
use Drupal\stanford_publication\Entity\CitationInterface;

/**
 * Class CitationViewBuilder.
 *
 * @package Drupal\stanford_publication
 */
class CitationViewBuilder extends EntityViewBuilder {

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
    $month = (int) trim(strip_tags(render($month)));
    $day = (int) trim(strip_tags(render($day)));

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
