<?php

namespace Drupal\cardinal_service_rest\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\FieldConfigInterface;
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
 *     "canonical" = "/api/opportunities"
 *   }
 * )
 */
class OpportunitiesResource extends ResourceBase {

  const ENTITY_TYPE = 'node';

  const BUNDLE = 'su_opportunity';

  /**
   * Entity Type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $fieldManager;

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
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, EntityTypeManagerInterface $entityTypeManager, EntityFieldManagerInterface $field_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityTypeManager = $entityTypeManager;
    $this->fieldManager = $field_manager;
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

    $fields = $this->fieldManager->getFieldDefinitions(self::ENTITY_TYPE, self::BUNDLE);

    foreach ($fields as $field_name => $field_definition) {
      if (
        $field_definition instanceof FieldConfig &&
        $field_definition->getType() == 'entity_reference' &&
        $field_definition->getSetting('handler') == 'default:taxonomy_term'
      ) {
        $data[$field_name] = $this->getFieldTermsData($field_definition);
      }
    }
    $data = array_filter($data);

    $response = new ResourceResponse();
    $response->setContent(json_encode($data));
    $response->addCacheableDependency($data);
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray([
      '#cache' => ['tags' => ['api:opportunities']],
    ]));

    return $response;
  }

  /**
   * Get the available taxonomy terms with references to the entity using it.
   *
   * @param \Drupal\field\FieldConfigInterface $field
   *
   * @return array
   *   Array of structured term data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getFieldTermsData(FieldConfigInterface $field) {
    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $node_storage = $this->entityTypeManager->getStorage('node');

    $data = [];
    $handler_settings = $field->getSetting('handler_settings');
    $vid = reset($handler_settings['target_bundles']);

    foreach ($term_storage->loadByProperties([
      'vid' => $vid,
      'status' => 1,
    ]) as $term) {
      $nodes = $node_storage->getQuery()
        ->condition('status', 1)
        ->condition($field->getName(), $term->id())
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
    uasort($data, function ($item_a, $item_b) {
      return count($item_a['items']) > count($item_b['items']) ? -1 : 1;
    });
    return array_values($data);
  }

}
