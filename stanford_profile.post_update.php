<?php

/**
 * @file
 * stanford_profile.install
 */

use Drupal\filter\Entity\FilterFormat;

/**
 * Remove entity_embed filter after stanford_media is done updating.
 */
function stanford_profile_post_update_8001() {
  /** @var \Drupal\filter\FilterFormatInterface $filter_format */
  foreach (FilterFormat::loadMultiple() as $filter_format) {
    $filters = $filter_format->get('filters');
    if (isset($filters['entity_embed'])) {
      $filter_format->filters();
      $filter_format->removeFilter('entity_embed');
      $filter_format->calculateDependencies();
      $filter_format->save();
    }
  }
}
