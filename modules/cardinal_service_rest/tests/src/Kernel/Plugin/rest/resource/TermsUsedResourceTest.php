<?php

namespace Drupal\Tests\cardinal_service_rest\Kernel\Plugin\rest\resource;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\rest\ResourceResponse;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class OpportunitiesResourceTest.
 *
 * @group cardinal_service_rest
 * @coversDefaultClass \Drupal\cardinal_service_rest\Plugin\rest\resource\TermsUsedResource
 */
class TermsUsedResourceTest extends KernelTestBase {

  protected static $modules = [
    'system',
    'taxonomy',
    'node',
    'user',
    'cardinal_service_rest',
    'rest',
    'field',
    'serialization',
    'text',
  ];

  public function setup(): void {
    parent::setUp();
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installEntitySchema('field_config');
    $this->installEntitySchema('taxonomy_term');

    NodeType::create([
      'name' => 'opportunity',
      'type' => 'su_opportunity',
    ])->save();

    $term_fields = [
      'su_opp_location' => 'su_opportunity_location',
      'su_opp_open_to' => 'su_opportunity_open_to',
      'su_opp_time_year' => 'su_opportunity_time',
      'su_opp_type' => 'su_opportunity_type',
    ];
    foreach ($term_fields as $term_field => $vocab) {
      Vocabulary::create(['vid' => $vocab, 'name' => $vocab])->save();
      $settings = [
        'handler' => 'default:taxonomy_term',
        'handler_settings' => [
          'target_bundles' => [$vocab => $vocab],
        ],
      ];
      $fieldStorage = FieldStorageConfig::create([
        'field_name' => $term_field,
        'entity_type' => 'node',
        'type' => 'entity_reference',
        'settings' => [
          'target_type' => 'taxonomy_term',
        ],
      ]);
      $fieldStorage->save();

      FieldConfig::create([
        'field_storage' => $fieldStorage,
        'bundle' => 'su_opportunity',
        'settings' => $settings,
      ])->save();

      for ($i = 0; $i < rand(10, 20); $i++) {
        $term = Term::create([
          'vid' => $vocab,
          'name' => $this->randomString(),
        ]);
        $term->save();
        $terms[$term_field][] = $term->id();
      }
    }

    for ($i = 0; $i <= 20; $i++) {
      $values = ['title' => "Page $i", 'type' => 'su_opportunity'];
      foreach (array_keys($term_fields) as $field) {
        $random_term = array_rand($terms[$field]);
        $values[$field] = $terms[$field][$random_term];
      }
      Node::create($values)->save();
    }
  }

  public function testResourceResponse() {
    /** @var \Drupal\rest\Plugin\Type\ResourcePluginManager $rest_manager */
    $rest_manager = \Drupal::service('plugin.manager.rest');
    $resource = $rest_manager->createInstance('terms_used_resource');
    $this->assertEmpty($resource->permissions());

    /** @var ResourceResponse $response */
    $response = $resource->get('su_opportunity');
    $this->assertInstanceOf(ResourceResponse::class, $response);

    $json_data = json_decode($response->getContent(), TRUE);
    $this->assertNotEmpty($json_data['su_opp_location']);
    $this->assertNotEmpty($json_data['su_opp_open_to']);
    $this->assertNotEmpty($json_data['su_opp_time_year']);
    $this->assertNotEmpty($json_data['su_opp_type']);
  }

}
