<?php

namespace Drupal\stanford_profile_drush\Commands;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\field\FieldConfigInterface;
use Drush\Commands\DrushCommands;

/**
 * Class StanfordProfileCommands.
 *
 * @package Drupal\stanford_profile\Commands
 */
class StanfordProfileCommands extends DrushCommands {

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Entity bundle service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $bundleInfo;

  /**
   * Entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * Field type plugin manager service.
   *
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface
   */
  protected $fieldTypeManager;

  /**
   * Drush command constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $bundleInfo
   *   Entity bundle service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $fieldManager
   *   Entity field manager service.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $fieldTypeManager
   *   Field type plugin manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, EntityTypeBundleInfoInterface $bundleInfo, EntityFieldManagerInterface $fieldManager, FieldTypePluginManagerInterface $fieldTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->bundleInfo = $bundleInfo;
    $this->fieldManager = $fieldManager;
    $this->fieldTypeManager = $fieldTypeManager;
  }

  /**
   * Generate a page with the possible combinations of components in rows.
   *
   * @command stanford-profile:stress-test-components
   * @aliases spstc
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

  /**
   * Generate the entity that holds all the react paragraphs.
   *
   * @param string $entity_type_id
   *   Entity type ID.
   * @param string $bundle
   *   Entity bundle name.
   * @param string $field
   *   React paragraphs field name.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function generateStressTestContent($entity_type_id, $bundle, $field) {
    $label_key = $this->entityTypeManager->getDefinition($entity_type_id)
      ->getKey('label');
    $bundle_key = $this->entityTypeManager->getDefinition($entity_type_id)
      ->getKey('bundle');

    /** @var \Drupal\field\FieldConfigInterface $field_config */
    $field_config = $this->entityTypeManager->getStorage('field_config')
      ->load("$entity_type_id.$bundle.$field");

    $this->entityTypeManager->getStorage($entity_type_id)->create([
      $label_key => 'Stress Test ' . date('F j Y'),
      $bundle_key => $bundle,
      $field => $this->getParagraphFieldValues($field_config),
    ])->save();
  }

  /**
   * Get the field value for the react paragraphs field with sample content.
   *
   * @param \Drupal\field\FieldConfigInterface $field
   *   React paragraphs field config object.
   *
   * @return array
   *   Field value list array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getParagraphFieldValues(FieldConfigInterface $field) {
    $paragraph_bundles = $this->bundleInfo->getBundleInfo('paragraph');
    $handler_settings = $field->getSetting('handler_settings');

    $paragraphs = [];
    foreach ($paragraph_bundles as $bundle => $bundle_info) {
      // Find out if the field is configured to allow the current bundle. The
      // field allows for "Exclude selected" which is the `negate` value. If a
      // paragraph has been added but the field settings haven't been resaved,
      // it may or may not be allowed in the field, so we check that too.
      if (isset($handler_settings['target_bundles_drag_drop'][$bundle])) {
        if ((bool) $handler_settings['negate'] === (bool) $handler_settings['target_bundles_drag_drop'][$bundle]['enabled']) {
          continue;
        }
      }
      elseif (!$handler_settings['negate']) {
        continue;
      }

      // Create a paragraph entity that we can clone so we can see the effects
      // at the different widths on the same display.
      $original_paragraph = $this->createParagraph($bundle);
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
   * Create a paragraph using field sample content.
   *
   * @param string $bundle
   *   Paragraph bundle name.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Generated paragraph with sample content.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createParagraph($bundle) {
    $bundle_fields = $this->fieldManager->getFieldDefinitions('paragraph', $bundle);
    $values = [
      'type' => $bundle,
    ];

    // Populate the paragraph with sample content in the fields.
    foreach ($bundle_fields as $field_name => $field_definition) {
      if (!$field_definition instanceof FieldConfigInterface) {
        continue;
      }
      $field_type = $field_definition->getType();
      $field_type_definition = $this->fieldTypeManager->getDefinition($field_type);
      $sample_value = NULL;

      try {
        $sample_value = $field_type_definition['class']::generateSampleValue($field_definition);
      }
      catch (\Exception $e) {
        // Move on to the next field.
        continue;
      }

      if ($sample_value) {
        // Alter the field samples because super long text without spaces does
        // no good. Nobody should put 255 characters into a field without any
        // spaces. I mean come on.
        $method = 'alterSample' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field_type)));
        if (method_exists($this, $method)) {
          self::$method($sample_value);
        }
        $values[$field_name] = $sample_value;
      }
    }
    $paragraph = $this->entityTypeManager->getStorage('paragraph')
      ->create($values);
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
    // Place random spaces into the string.
    for ($i = 0; $i < strlen($sample_value['value']); $i += 12) {
      $space_location = rand($i - 10, $i);
      $new_value = substr($new_value, 0, $space_location) . ' ' . substr($new_value, $space_location);
    }
    // Chop the string down. We don't need super long strings.
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
