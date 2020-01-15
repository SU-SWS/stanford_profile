<?php

/**
 * @file
 * stanford_profile.install
 */

use Drupal\filter\Entity\FilterFormat;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityViewDisplay;

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

/**
 * Change paragraphs fields into react paragraph fields.
 */
function stanford_profile_post_update_8003() {
  \Drupal::service('module_installer')->install(['react_paragraphs']);

  $database = \Drupal::database();
  $tables = [
    'node__su_page_components',
    'node_revision__su_page_components',
  ];

  $definition = [
    'description' => 'Settings for the item.',
    'type' => 'blob',
    'size' => 'normal',
  ];
  foreach ($tables as $table) {
    $database->schema()
      ->addField($table, 'su_page_components_settings', $definition);

    $results = $database->select($table, 's')
      ->fields('s')
      ->execute();
    while ($row = $results->fetchAssoc()) {
      $settings = [
        'row' => $row['delta'],
        'index' => 0,
        'width' => 12,
        'admin_title' => '',
      ];
      $database->update($table)
        ->fields(['su_page_components_settings' => json_encode($settings)])
        ->condition('entity_id', $row['entity_id'])
        ->condition('revision_id', $row['revision_id'])
        ->condition('delta', $row['delta'])
        ->execute();
    }
  }

  $config_factory = \Drupal::configFactory();
  $config_factory->getEditable('field.storage.node.su_page_components')
    ->set('module', 'react_paragraphs')
    ->set('type', 'react_paragraphs')
    ->set('cardinality', 1)
    ->save();

  $config_factory->getEditable('field.field.node.stanford_page.su_page_components')
    ->set('field_type', 'react_paragraphs')
    ->save();

  /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $form_display */
  $form_display = EntityFormDisplay::load('node.stanford_page.default');
  $form_display->removeComponent('su_page_components');
  $form_display->setComponent('su_page_components', ['weight' => 9]);
  $form_display->save();

  /** @var \Drupal\layout_builder\Entity\LayoutBuilderEntityViewDisplay $view_display */
  $view_display = EntityViewDisplay::load('node.stanford_page.default');
  foreach ($view_display->getSections() as $section) {
    foreach ($section->getComponents() as $component) {
      if (strpos($component->getPluginId(), 'su_page_components') !== FALSE) {
        $config = $component->get('configuration');
        $config['formatter']['type'] = 'react_paragraphs';
        $component->setConfiguration($config);
      }
    }
  }
  $view_display->save();
}
