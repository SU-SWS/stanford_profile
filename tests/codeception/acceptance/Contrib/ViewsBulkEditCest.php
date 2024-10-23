<?php

use Faker\Factory;

/**
 * Test out the views bulk edit module.
 *
 * @group contrib
 * @group bulk_edit
 */
class ViewsBulkEditCest {

  /**
   * @var \Drupal\node\NodeInterface[]
   */
  protected $nodes;

  /**
   * Faker generator.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Bulk editing content changes the field values.
   */
  public function testBulkEdits(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $this->createEvents($I);

    $event_foo_bar_baz = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_event_types',
    ], 'taxonomy_term');
    $news_foo_bar_baz = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_news_topics',
    ], 'taxonomy_term');
    $pubs_foo_bar_baz = $I->createEntity([
      'name' => $this->faker->words(3, TRUE),
      'vid' => 'stanford_publication_topics',
    ], 'taxonomy_term');

    $I->amOnPage('/admin/content');
    $I->selectOption('Action', 'Modify field values');
    foreach ($this->nodes as $delta => $node) {
      $I->canSee($node->label(), 'tr');
      $I->checkOption('tr:contains("' . $node->label() . '") input[name^="views_bulk_operations_bulk_form"]');
    }
    $I->click('Apply to selected items');
    $I->canSee('Items selected');
    foreach ($this->nodes as $node) {
      $I->canSee($node->label());
    }
    $I->checkOption('News Types (value 1)');
    $I->selectOption('node[stanford_news][su_news_topics][0][target_id]', $news_foo_bar_baz->id());
    $I->checkOption('Event Types (value 1)');
    $I->selectOption('node[stanford_event][su_event_type][0][target_id]', $event_foo_bar_baz->id());
    $I->fillField('node[stanford_event][su_event_date_time][0][time_wrapper][value][date]', date('Y-m-d'));
    $I->fillField('node[stanford_event][su_event_date_time][0][time_wrapper][value][time]', '12:00:00');
    $I->fillField('node[stanford_event][su_event_date_time][0][time_wrapper][end_value][date]', date('Y-m-d'));
    $I->fillField('node[stanford_event][su_event_date_time][0][time_wrapper][end_value][time]', '12:00:00');

    $I->checkOption('Publication Types (value 1)');
    $I->selectOption('node[stanford_publication][su_publication_topics][0][target_id]', $pubs_foo_bar_baz->id());
    $I->click('Apply');
    $I->canSee('Modify field values', '.messages-list');

    foreach ($this->nodes as $node) {
      $I->amOnPage($node->toUrl('edit-form')->toString());

      switch ($node->bundle()) {
        case 'stanford_event':
          $I->canSeeOptionIsSelected('Event Types (value 1)', $event_foo_bar_baz->label());
          break;

        case 'stanford_news':
          $I->canSeeOptionIsSelected('News Types (value 1)', $news_foo_bar_baz->label());
          break;

        case 'stanford_publication':
          $I->canSeeOptionIsSelected('Publication Types (value 1)', $pubs_foo_bar_baz->label());
          break;
      }
    }
  }

  /**
   * Create a bunch of nodes.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   */
  protected function createEvents(AcceptanceTester $I) {
    $faker = Factory::create();
    $node_types = [
      'stanford_event',
      'stanford_news',
      'stanford_publication',
    ];
    foreach ($node_types as $node_type) {
      for ($j = 0; $j <= 5; $j++) {
        $this->nodes[] = $I->createEntity([
          'type' => $node_type,
          'title' => $faker->text(30),
        ]);
      }
    }
  }

}
