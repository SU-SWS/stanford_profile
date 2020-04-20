<?php

namespace Drupal\cardinal_service_rest\Plugin\rest\resource;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides a Demo Resource
 * @RestResource(
 *   id = "opportunities_resource",
 *   label = @Translation("Opportunities Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/opportunties"
 *   }
 * )
 */
class OpportunitiesResource extends ResourceBase {

  /**
   * Entity Type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritDoc}
   */
  public function permissions() {
    return [];
  }

  public function get() {
    $vocabs = [
      'su_opp_location' => 'su_opportunity_location',
      'su_opp_open_to' => 'su_opportunity_open_to',
      'su_opp_time_year' => 'su_opportunity_time',
      'su_opp_type' => 'su_opportunity_type',
    ];
    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $node_storage = $this->entityTypeManager->getStorage('node');
    $data = [];

    foreach ($vocabs as $field => $vid) {
      foreach ($term_storage->loadByProperties([
        'vid' => $vid,
        'status' => 1,
      ]) as $term) {
        $nodes = $node_storage->getQuery()
          ->condition('status', 1)
          ->condition($field, $term->id())
          ->execute();

        if ($nodes) {
          $data[$field][] = [
            'id' => $term->id(),
            'label' => $term->label(),
            'items' => array_values($nodes),
          ];
        }
      }
    }

    dpm($data);
    foreach ($data as &$values) {
      // Sort the terms based on how many nodes they have.
      uasort($values, function ($item_a, $item_b) {
        return count($item_a['items']) > count($item_b['items']) ? -1 : 1;
      });
      // Reset the term data so that the result will be a json array instead of
      // an object.
      $values = array_values($values);
    }

    return new JsonResponse($data);
  }

}
