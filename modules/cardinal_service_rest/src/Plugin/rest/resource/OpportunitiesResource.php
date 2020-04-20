<?php

namespace Drupal\cardinal_service_rest\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
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
    $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $data = [];

    foreach ($vocabs as $field => $vid) {
      foreach ($term_storage->loadByProperties(['vid' => $vid]) as $term) {
        $nodes = $node_storage->getQuery()
          ->condition($field, $term->id())
          ->execute();
        $data[$field][] = [
          'id' => $term->id(),
          'label' => $term->label(),
          'items' => array_keys($nodes),
        ];
      }
    }
    foreach ($data as &$values) {
      $values = array_filter($values);
      uasort($values, function ($item_a, $item_b) {
        return count($item_a['items']) > count($item_b['items']) ? -1 : 1;
      });
      $values = array_values($values);
    }

    return new JsonResponse($data);
  }

}
