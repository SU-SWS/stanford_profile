<?php

namespace Drupal\cardinal_service_profile_helper\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileSystemInterface;
use Drupal\migrate_plus\Entity\MigrationInterface;
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
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('file_system')
    );
  }

  /**
   * NodeCsvTemplate constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   File system service.
   */
  public function __construct(FileSystemInterface $file_system) {
    $this->fileSystem = $file_system;
  }

  /**
   * Controller callback to download a csv with field headers for csv importer.
   *
   * @param \Drupal\migrate_plus\Entity\MigrationInterface $migration
   *   Migration entity object.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *   Downloadable file.
   */
  public function getTemplate(MigrationInterface $migration) {
    $file_name = $migration->id() . '.csv';
    $uri = 'temporary://' . $file_name;

    $headers = $this->getCsvHeaders($migration);
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
   * @param \Drupal\migrate_plus\Entity\MigrationInterface $migration
   *   Migration entity object..
   *
   * @return string[]
   *   Array of headers for the CSV.
   */
  protected function getCsvHeaders(MigrationInterface $migration) {
    $csv_headers = [];
    foreach ($migration->source['fields'] as $source_field) {
      $csv_headers[] = sprintf('%s (%s)', $source_field['selector'], $source_field['label']);
    }
    return $csv_headers;
  }

}
