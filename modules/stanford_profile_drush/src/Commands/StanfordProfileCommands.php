<?php

namespace Drupal\stanford_profile_drush\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field\FieldConfigInterface;
use Drush\Commands\DrushCommands;

/**
 * Class StanfordProfileCommands.
 *
 * @package Drupal\stanford_profile\Commands
 */
class StanfordProfileCommands extends DrushCommands {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $bundleInfo;

  /**
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->bundleInfo = \Drupal::service('entity_type.bundle.info');
    $this->fieldManager = \Drupal::service('entity_field.manager');
  }

  /**
   * Generate a page with the possible combinations of components in rows.
   *
   * @command stanford-profile:stress-test-components
   * @alias spstc
   */
  public function generateStressTestNode() {
    $fields_map = $this->fieldManager->getFieldMapByFieldType('react_paragraphs');
    foreach ($fields_map as $entity_type_id => $fields) {
      foreach ($fields as $field_name => $bundles) {
        foreach ($bundles['bundles'] as $bundle) {
          $this->generateStressTestContent($entity_type_id, $bundle, $field_name);
        }
      }
    }
  }

  protected function generateStressTestContent($entity_type_id, $bundle, $field) {
    $label_key = $this->entityTypeManager->getDefinition($entity_type_id)
      ->getKey('label');
    $bundle_key = $this->entityTypeManager->getDefinition($entity_type_id)
      ->getKey('bundle');

    /** @var \Drupal\field\FieldConfigInterface $field_config */
    $field_config = $this->entityTypeManager->getStorage('field_config')
      ->load("$entity_type_id.$bundle.$field");

    $this->getParagraphFieldValues($field_config);
    $this->entityTypeManager->getStorage($entity_type_id)->create([
      $label_key => 'Stress Test ' . date('F j Y'),
      $bundle_key => $bundle,
      $field => $this->getParagraphFieldValues($field_config),
    ])->save();
  }

  protected function getParagraphFieldValues(FieldConfigInterface $field) {
    $paragraph_bundles = $this->bundleInfo->getBundleInfo('paragraph');
    $handler_settings = $field->getSetting('handler_settings');

    $paragraphs = [];
    foreach ($paragraph_bundles as $bundle => $bundle_info) {
      if (
        (!isset($handler_settings['target_bundles_drag_drop'][$bundle]) && !$handler_settings['negate']) ||
        (bool) $handler_settings['negate'] === (bool) $handler_settings['target_bundles_drag_drop'][$bundle]['enabled']) {
        continue;
      }

      $original_paragraph = $this->createParagraph($bundle, $bundle_info['label']);
      $paragraphs[] = [
        'target_id' => $original_paragraph->id(),
        'target_revision_id' => $original_paragraph->getRevisionId(),
        'settings' => [
          'row' => count($paragraphs),
          'index' => 0,
          'width' => 12,
        ],
      ];

      // Take the original paragraph, clone it and make it smaller.
      foreach ([6, 4, 3] as $width) {
        $new_paragraph = $original_paragraph->createDuplicate();
        $new_paragraph->save();
        $paragraphs[] = [
          'target_id' => $new_paragraph->id(),
          'target_revision_id' => $new_paragraph->getRevisionId(),
          'settings' => [
            'row' => count($paragraphs),
            'index' => 0,
            'width' => $width,
          ],
        ];
      }
    }

    return $paragraphs;
  }

  /**
   * @param $bundle
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function createParagraph($bundle) {
    $bundle_fields = $this->fieldManager->getFieldDefinitions('paragraph', $bundle);
    $values = [
      'type' => $bundle,
    ];

    foreach ($bundle_fields as $field_name => $field_definition) {
      if (!$field_definition instanceof FieldConfigInterface) {
        continue;
      }
      $field_type = $field_definition->getType();
      /** @var \Drupal\Core\Field\FieldTypePluginManager $field_type_manager */
      $field_type_manager = \Drupal::service('plugin.manager.field.field_type');
      $field_type_definition = $field_type_manager->getDefinition($field_type);

      try {
        $sample_value = $field_type_definition['class']::generateSampleValue($field_definition);
        if ($sample_value) {
          $method = 'alterSample' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field_type)));

          if (method_exists($this, $method)) {
            self::$method($sample_value);
          }
          $values[$field_name] = $sample_value;
        }
      } catch (\Exception $e) {
        // Move on to the next field.
      }
    }
    $paragraph = $this->entityTypeManager->getStorage('paragraph')->create($values);
    $paragraph->save();
    return $paragraph;
  }

  /**
   * Alter the sample field value for the string field type.
   *
   * @param mixed $sample_value
   *   Sample value from the field.
   */
  protected static function alterSampleString(&$sample_value) {
    $new_value = $sample_value['value'];
    for ($i = 0; $i < strlen($sample_value['value']); $i += 12) {
      $space_location = rand($i - 10, $i);
      $new_value = substr($new_value, 0, $space_location) . ' ' . substr($new_value, $space_location);
    }
    $sample_value['value'] = substr($new_value, 0, 50);
  }

  /**
   * Alter the sample field value for the long text field type.
   *
   * @param mixed $sample_value
   *   Sample value from the field.
   */
  protected static function alterSampleTextLong(&$sample_value) {
    $sample_value['value'] = substr($sample_value['value'], 0, 250);
  }

  /**
   * Alter the sample field value for the link field type.
   *
   * @param mixed $sample_value
   *   Sample value from the field.
   */
  protected static function alterSampleLink(&$sample_value) {
    if (isset($sample_value['title'])) {
      $sample_value['title'] = substr($sample_value['title'], 0, 15);
    }
  }

}
