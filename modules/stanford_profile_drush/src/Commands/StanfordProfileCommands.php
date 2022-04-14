<?php

namespace Drupal\stanford_profile_drush\Commands;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
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
   * Array of original paragraphs with the key as the paragraph bundle.
   *
   * @var \Drupal\paragraphs\ParagraphInterface[]
   */
  protected $paragraphs;

  /**
   * Name of the content to create.
   *
   * @var string
   */
  protected $name;

  /**
   * Array of paragraph bundles to skip.
   *
   * @var array
   */
  protected $exclude;

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
    parent::__construct();
    $this->entityTypeManager = $entityTypeManager;
    $this->bundleInfo = $bundleInfo;
    $this->fieldManager = $fieldManager;
    $this->fieldTypeManager = $fieldTypeManager;
  }

  /**
   * Generate a page with the possible combinations of components in rows.
   *
   * @option name
   *   Name the node something specific.
   * @option exclude
   *   Comma separated list of paragraphs to skip.
   *
   * @command stanford-profile:stress-test-components
   * @aliases spstc
   *
   * @params array $options
   *   Keyed array of command options.
   */
  public function generateStressTestNode(array $options = [
    'name' => NULL,
    'exclude' => '',
  ]) {
    $this->name = $options['name'] ?? 'Stress Test ' . date('F j Y');
    $this->exclude = explode(',', $options['exclude']);

    $fields_map = $this->fieldManager->getFieldMapByFieldType('entity_reference_revisions');
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

    if ($field_config->getSetting('handler') != 'default:paragraph_row') {
      return;
    }

    $this->entityTypeManager->getStorage($entity_type_id)->create([
      $label_key => $this->name,
      $bundle_key => $bundle,
      $field => $this->getRowFieldValues($field_config),
    ])->save();
  }

  /**
   * Build the rows with paragraphs on them.
   *
   * @param \Drupal\field\FieldConfigInterface $field
   *   Row entity reference field entity.
   *
   * @return array
   *   Array of field values for the given field.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getRowFieldValues(FieldConfigInterface $field): array {
    $row_bundle = $this->getRowBundle($field);
    $row_fields = $this->fieldManager->getFieldDefinitions($field->getSetting('target_type'), $row_bundle);

    $bundle_key = $this->entityTypeManager->getDefinition($field->getSetting('target_type'))
      ->getKey('bundle');

    $row_values = [];

    foreach ($row_fields as $row_field) {
      if ($row_field instanceof FieldConfigInterface && $row_field->getType() == 'entity_reference_revisions') {
        foreach ($this->getParagraphBundles($row_field) as $paragraph_bundle) {
          foreach ([12, 6, 4, 3] as $width) {

            $row = $this->entityTypeManager->getStorage($field->getSetting('target_type'))
              ->create([
                $bundle_key => $row_bundle,
                $row_field->getName() => $this->getParagraphFieldValues($paragraph_bundle, $width),
              ]);
            $row->save();

            $row_values[] = [
              'target_id' => $row->id(),
              'target_revision_id' => $row->getRevisionId(),
            ];
          }
        }
      }
    }
    return $row_values;
  }

  /**
   * Get the paragraph row bundle name.
   *
   * @param \Drupal\field\FieldConfigInterface $field
   *   Entity reference field.
   *
   * @return string
   *   Row bundle name.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getRowBundle(FieldConfigInterface $field) {
    $handler_settings = $field->getSetting('handler_settings');
    $row_bundle = key($handler_settings['target_bundles']);

    if ($row_bundle) {
      return $row_bundle;
    }
    $row_types = $this->entityTypeManager->getStorage('paragraphs_row_type')
      ->loadMultiple();
    return key($row_types);
  }

  /**
   * Build paragraphs and load them into an array for the row field values.
   *
   * @param string $bundle
   *   Paragraph bundle name.
   * @param int $width
   *   Width of the paragraph to build.
   *
   * @return array
   *   Field value list array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getParagraphFieldValues($bundle, $width): array {
    $paragraphs = [];

    for ($i = 1; $i <= 12 / $width; $i++) {
      /** @var \Drupal\paragraphs\ParagraphInterface $new_paragraph */
      $paragraph = $this->createParagraph($bundle);
      $paragraph->setBehaviorSettings('react', [
        'width' => $width,
        'label' => "$width columns",
      ]);
      $paragraph->save();
      $paragraphs[] = [
        'target_id' => $paragraph->id(),
        'target_revision_id' => $paragraph->getRevisionId(),
      ];
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
  protected function createParagraph($bundle): EntityInterface {
    // We've already created this paragraph type, just duplicated i.
    if (isset($this->paragraphs[$bundle])) {
      return $this->paragraphs[$bundle]->createDuplicate();
    }

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
    $paragraph->setBehaviorSettings('react', [
      'width' => 12,
      'label' => '12 columns',
    ]);
    $paragraph->save();
    $this->paragraphs[$bundle] = $paragraph;
    return $paragraph;
  }

  /**
   * Based on the field settings, get the available paragraph bundles.
   *
   * @param \Drupal\field\FieldConfigInterface $paragraph_field
   *   Field entity targeting paragraphs.
   *
   * @return array
   *   Array of paragraph bundle machine names.
   */
  protected function getParagraphBundles(FieldConfigInterface $paragraph_field): array {
    $paragraph_bundles = $this->bundleInfo->getBundleInfo('paragraph');
    $handler_settings = $paragraph_field->getSetting('handler_settings');
    $allowed_bundles = [];
    foreach (array_keys($paragraph_bundles) as $bundle) {
      // Find out if the field is configured to allow the current bundle. The
      // field allows for "Exclude selected" which is the `negate` value. If a
      // paragraph has been added but the field settings haven't been resaved,
      // it may or may not be allowed in the field, so we check that too.
      if (isset($handler_settings['target_bundles_drag_drop'][$bundle])) {
        if ((bool) $handler_settings['negate'] === (bool) $handler_settings['target_bundles_drag_drop'][$bundle]['enabled']) {
          continue;
        }
      }
      elseif (isset($handler_settings['negate']) && !$handler_settings['negate']) {
        continue;
      }
      $allowed_bundles[] = $bundle;
    }
    $allowed_bundles = array_diff($allowed_bundles, $this->exclude);
    return array_filter($allowed_bundles);
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
