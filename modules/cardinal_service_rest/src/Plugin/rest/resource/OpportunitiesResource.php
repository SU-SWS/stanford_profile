<?php

namespace Drupal\cardinal_service_rest\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Rest endpoint to provide data about what entities are tagged with terms.
 *
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
   * Associative array of field => taxonomy vocab.
   *
   * @var string[]
   */
  protected $vocabs = [
    'su_opp_location' => 'su_opportunity_location',
    'su_opp_open_to' => 'su_opportunity_open_to',
    'su_opp_time_year' => 'su_opportunity_time',
    'su_opp_type' => 'su_opportunity_type',
    'su_opp_dimension' => 'su_opportunity_dimension',
    'su_opp_pathway' => 'su_opportunity_pathway',
    'su_opp_placement_type' => 'su_opportunity_placement_type',
    'su_opp_service_theme' => 'su_opportunity_service_theme',
  ];

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

  /**
   * Get request on the rest endpoint.
   *
   * @return \Drupal\rest\ResourceResponse
   *   Rest response.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function get() {
    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $node_storage = $this->entityTypeManager->getStorage('node');
    $data = [];

    foreach ($this->vocabs as $field => $vid) {
      $data[$field] = [];

      foreach ($term_storage->loadByProperties([
        'vid' => $vid,
        'status' => 1,
      ]) as $term) {
        $nodes = $node_storage->getQuery()
          ->condition('status', 1)
          ->condition($field, $term->id())
          ->accessCheck(FALSE)
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

    foreach ($data as &$values) {
      // Sort the terms based on how many nodes they have.
      uasort($values, function ($item_a, $item_b) {
        return count($item_a['items']) > count($item_b['items']) ? -1 : 1;
      });
      // Reset the term data so that the result will be a json array instead of
      // an object.
      $values = array_values($values);
    }

    $response = new ResourceResponse();
    $response->setContent(json_encode($data));
    $response->addCacheableDependency($data);
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray([
      '#cache' => ['tags' => ['api:opportunities']],
    ]));

    return $response;
  }

}
