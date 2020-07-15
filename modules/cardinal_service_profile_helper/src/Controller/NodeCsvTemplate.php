<?php

namespace Drupal\cardinal_service_profile_helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\node\NodeTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class NodeCsvTemplate.
 *
 * @package Drupal\cardinal_service_profile\Controller
 */
class NodeCsvTemplate extends ControllerBase {

  /**
   * File system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system'),
      $container->get('entity_field.manager')
    );
  }

  /**
   * NodeCsvTemplate constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   File system service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $field_manager
   *   Entity field manager service.
   */
  public function __construct(FileSystemInterface $file_system, EntityFieldManagerInterface $field_manager) {
    $this->fileSystem = $file_system;
    $this->fieldManager = $field_manager;
  }

  /**
   * Controller callback to download a csv with field headers for csv importer.
   *
   * @param \Drupal\node\NodeTypeInterface $node_type
   *   Node type entity object.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   Downloadable file.
   */
  public function getTemplate(NodeTypeInterface $node_type) {
    $file_name = $node_type->id() . '.csv';
    $uri = 'temporary://' . $file_name;

    $headers = $this->getCsvHeaders($node_type);
    $uri = $this->fileSystem->saveData(implode(',', $headers), $uri, FileSystemInterface::EXISTS_REPLACE);
    $headers = [
      'Content-Type' => 'text/csv',
      'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
    ];
    return new BinaryFileResponse($uri, 200, $headers, FALSE);
  }

  /**
   * Get the list of field headers for the csv file.
   *
   * @param \Drupal\node\NodeTypeInterface $node_type
   *   Node type entity object.
   *
   * @return string[]
   *   Array of headers for the CSV.
   */
  protected function getCsvHeaders(NodeTypeInterface $node_type) {
    $fields = $this->fieldManager->getFieldDefinitions('node', $node_type->id());

    $fields = array_filter($fields, function ($field) {
      return $field instanceof FieldConfigInterface;
    });
    $csv_headers = ['title'];
    /** @var \Drupal\field\FieldConfigInterface $field */
    foreach ($fields as $field) {
      $csv_headers = array_merge($csv_headers, $this->getCsvHeadersForField($field));
    }

    return $csv_headers;
  }

  /**
   * Get the list of CSV headers for the single field.
   *
   * @param \Drupal\field\FieldConfigInterface $field
   *   Field config entity.
   *
   * @return string[]
   *   List of field column headers.
   */
  protected function getCsvHeadersForField(FieldConfigInterface $field) {
    $columns = $field->getFieldStorageDefinition()->getColumns();
    unset($columns['options'], $columns['format']);
    $csv_headers = [];

    if (in_array($field->getType(), [
      'entity_reference_revisions',
      'entity_reference',
      'layout_section',
    ])) {
      return [];
    }

    if (count($columns) == 1) {
      return [$field->getName()];
    }
    foreach (array_keys($columns) as $column) {
      $csv_headers[] = "{$field->getName()}|$column";
    }
    return $csv_headers;
  }

}
