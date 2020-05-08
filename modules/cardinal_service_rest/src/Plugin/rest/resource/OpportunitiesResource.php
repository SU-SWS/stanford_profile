<?php

namespace Drupal\cardinal_service_rest\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Rest endpoint to provide data about what entities are tagged with terms.
 *
 * @RestResource(
 *   id = "opportunities_resource",
 *   label = @Translation("Opportunities Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/opportunities"
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
   * Current page request.
   *
   * @var \Symfony\Component\HttpFoundation\Request|null
   */
  protected $currentRequest;

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
      $container->get('entity_type.manager'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, EntityTypeManagerInterface $entityTypeManager, RequestStack $request_stack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityTypeManager = $entityTypeManager;
    $this->currentRequest = $request_stack->getCurrentRequest();
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
    $data = [];
    $fields = $this->currentRequest->query->get('fields');
    $bundle = $this->currentRequest->query->get('bundle');

    if (empty($fields) || empty($bundle)) {
      return new ResourceResponse([]);
    }

    foreach ($fields as $field_name) {
      $data[$field_name] = $this->getFieldTermsData($bundle, $field_name);
    }
    $data = array_filter($data);

    $response = new ResourceResponse();
    $response->setContent(json_encode($data));
    $response->addCacheableDependency($data);
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray([
      '#cache' => ['keys' => $fields, 'tags' => ['api:opportunities']],
    ]));

    return $response;
  }

  /**
   * Get the available taxonomy terms with references to the entity using it.
   *
   * @param string $bundle
   *   Node type bundle.
   * @param string $field_name
   *   Field machine name.
   *
   * @return array
   *   Array of structured term data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getFieldTermsData($bundle, $field_name) {
    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $node_storage = $this->entityTypeManager->getStorage('node');

    $data = [];

    if ($vid = $this->getFieldVocab($bundle, $field_name)) {
      foreach ($term_storage->loadByProperties([
        'vid' => $vid,
        'status' => 1,
      ]) as $term) {
        $nodes = $node_storage->getQuery()
          ->condition('status', 1)
          ->condition($field_name, $term->id())
          ->accessCheck(FALSE)
          ->execute();

        if ($nodes) {
          $data[] = [
            'id' => $term->id(),
            'label' => $term->label(),
            'items' => array_values($nodes),
          ];
        }
      }
    }
    uasort($data, function ($item_a, $item_b) {
      return count($item_a['items']) > count($item_b['items']) ? -1 : 1;
    });
    return array_values($data);
  }

  /**
   * Get the vocabulary ID from the field on the given node bundle.
   *
   * @param string $bundle
   *   Node type bundle.
   * @param string $field_name
   *   Field machine name.
   *
   * @return string|null
   *   Vocabulary id or null if none exists.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getFieldVocab($bundle, $field_name) {
    /** @var \Drupal\field\FieldConfigInterface $field */
    $field = $this->entityTypeManager->getStorage('field_config')
      ->load("node.$bundle.$field_name");
    if ($field && $field->getType() == 'entity_reference') {
      $handler_settings = $field->getSetting('handler_settings');
      return reset($handler_settings['target_bundles']);
    }
  }

}
