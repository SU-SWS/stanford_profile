<?php

namespace Drupal\cardinal_service_rest\Plugin\rest\resource;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\FieldConfigInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\taxonomy\TermInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Rest endpoint to provide data about what entities are tagged with terms.
 *
 * @RestResource(
 *   id = "terms_used_resource",
 *   label = @Translation("Terms In Use Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/terms-used/{node_type}"
 *   }
 * )
 */
class TermsUsedResource extends ResourceBase {

  const ENTITY_TYPE = 'node';

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
   * Current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request|null
   */
  protected $request;

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
      $container->get('entity_field.manager'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, EntityTypeManagerInterface $entityTypeManager, EntityFieldManagerInterface $field_manager, RequestStack $request_stack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->entityTypeManager = $entityTypeManager;
    $this->fieldManager = $field_manager;
    $this->request = $request_stack->getCurrentRequest();
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
   * @param string $node_type
   *   Node type bundle id.
   * @param bool $include_children
   *   If the data should include all children terms as well.
   *
   * @return \Drupal\rest\ResourceResponse
   *   Rest response.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function get($node_type, $include_children = TRUE) {
    $data = [];

    $fields = $this->fieldManager->getFieldDefinitions(self::ENTITY_TYPE, $node_type);
    foreach ($fields as $field_name => $field_definition) {
      if (
        $field_definition instanceof FieldConfig &&
        $field_definition->getType() == 'entity_reference' &&
        $field_definition->getSetting('handler') == 'default:taxonomy_term'
      ) {
        $data[$field_name] = $this->getFieldTermsData($field_definition, $include_children);
      }
    }

    $params = $this->request->query->all();
    $filtering_params = array_intersect(array_keys($params), array_keys($data));
    $filtered_ids = [];

    if ($filtering_params) {
      foreach ($filtering_params as $field) {
        foreach ($params[$field] as $value) {
          $key = array_search($value, array_column($data[$field], 'id'));
          $filtered_ids = $filtered_ids ? array_intersect($filtered_ids, $data[$field][$key]['items']) : $data[$field][$key]['items'];
        }
      }
    }

    if ($filtered_ids) {
      foreach ($data as &$field_values) {
        foreach ($field_values as $key => &$field_value) {
          $field_value['items'] = array_values(array_intersect($filtered_ids, $field_value['items']));
          if (!$field_value['items']) {
            unset($field_values[$key]);
          }
        }

        $field_values = array_values($field_values);
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
   *   Taxonomy field config entity.
   * @param bool $include_children
   *   If the data should include all children terms as well.
   *
   * @return array
   *   Array of structured term data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getFieldTermsData(FieldConfigInterface $field, $include_children = FALSE) {
    $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    $node_storage = $this->entityTypeManager->getStorage('node');

    $data = [];
    $handler_settings = $field->getSetting('handler_settings');
    $vid = reset($handler_settings['target_bundles']);

    foreach ($term_storage->loadByProperties([
      'vid' => $vid,
      'status' => 1,
    ]) as $term) {
      $term_ids = $include_children ? $this->getChildrenIds($term) : [$term->id()];

      $query = $node_storage->getQuery()
        ->accessCheck()
        ->condition('status', 1)
        ->condition('type', $field->getTargetBundle())
        ->condition($field->getName(), $term_ids, 'IN');

      if ($field->getTargetBundle() == 'su_spotlight') {
        $query->condition('body', '', '!=');
      }

      $nodes = $query->accessCheck(FALSE)
        ->execute();

      if ($nodes) {
        $data[] = [
          'id' => $term->id(),
          'label' => $term->label(),
          'items' => array_values($nodes),
        ];
      }
    }

    uasort($data, function($item_a, $item_b) {
      return count($item_a['items']) > count($item_b['items']) ? -1 : 1;
    });
    return array_values($data);
  }

  /**
   * Get an array of term ids with all children terms under it.
   *
   * @param \Drupal\taxonomy\TermInterface $term
   *   Parent taxonomy term.
   *
   * @return array
   *   Array of taxonomy term ids.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getChildrenIds(TermInterface $term) {
    $term_ids = [$term->id()];
    $child_terms = $this->entityTypeManager->getStorage('taxonomy_term')
      ->loadByProperties(['parent' => $term->id()]);
    foreach ($child_terms as $child_term) {
      $term_ids[] = $child_term->id();
      $term_ids[] = $this->getChildrenIds($child_term);
    }
    // Flatten the array.
    $term_ids = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($term_ids)), FALSE);
    // Remove duplicates.
    return array_values(array_unique($term_ids));
  }

}
