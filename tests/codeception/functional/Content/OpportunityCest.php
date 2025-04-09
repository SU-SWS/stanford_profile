<?php

use Faker\Factory;

/**
 * Test opportunity content type.
 *
 * @group opportunity
 */
class OpportunityCest {

  /**
   * Faker.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  protected function testContentType(FunctionalTester $I) {
    [
      $parent_1,
      $parent_2,
      $child_1_1,
      $child_2_1,
      $child_1_2,
      $child_2_2,
    ] = $this->buildTaxonomyTerms($I);

    $node = $I->createEntity([
      'type' => 'stanford_opportunity',
      'title' => $this->faker->words(3, TRUE),
    ]);

    $I->logInWithRole('contributor');

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->click('#edit-group-taxonomy summary');
    $I->canSee($parent_1->label(), 'legend');
    $I->canSee($parent_2->label(), 'legend');

    $parent_1_id = preg_replace('@[^a-z0-9_.]+@', '_', mb_strtolower($parent_1->label()));
    $parent_2_id = preg_replace('@[^a-z0-9_.]+@', '_', mb_strtolower($parent_2->label()));

    $I->selectOption("#$parent_1_id select.simpler-select", $child_1_1->label());
    $I->click('Add More', "#$parent_1_id");
    $I->waitForElementVisible("#$parent_1_id [class*='1-target-id'] select.simpler-select");
    $I->selectOption("#$parent_1_id [class*='1-target-id'] select.simpler-select", $child_1_2->label());

    $I->selectOption("#$parent_2_id select.simpler-select", $child_2_1->label());

    $I->waitForElementVisible("#$parent_2_id [class*='--level-1'] select.simpler-select");
    $I->selectOption("#$parent_2_id [class*='--level-1'] select.simpler-select", $child_2_2->label());

    $I->click('Save');
    $I->canSee($node->label(), 'h1');

    $I->canSee($parent_1->label(), 'h2');
    $I->canSee($child_1_1->label());
    $I->canSee($child_1_2->label());

    $I->canSee($parent_2->label(), 'h2');
    $I->canSee($child_2_1->label());
    $I->canSee($child_2_2->label());
  }

  public function testFilteringOpportunities(FunctionalTester $I) {
    [
      $parent_1,
      $parent_2,
      $child_1_1,
      $child_2_1,
      $child_1_2,
      $child_2_2,
    ] = $this->buildTaxonomyTerms($I);
    $opportunity = $I->createEntity([
      'type' => 'stanford_opportunity',
      'title' => $this->faker->words(3, TRUE),
      'su_opp_tags' => [
        ['target_id' => $child_1_1->id()],
        ['target_id' => $child_2_1->id()],
      ],
    ]);
    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
      'su_list_headline' => $this->faker->words(3, TRUE),
      'su_filtered_list_view' => [
        'target_id' => 'stanford_opportunities_filtered',
        'display_id' => 'cards',
        'arguments' => '',
        'items_to_display' => NULL,
      ],
    ], 'paragraph');
    $page = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
    $I->amOnPage($page->toUrl()->toString());
    $I->canSee($opportunity->label());

    $I->waitForText($parent_1->label(), 2, 'fieldset');

    $I->checkOption($child_1_2->label());
    $I->waitForAjaxToFinish();
    $I->cantSee($opportunity->label());

    $I->checkOption($child_1_1->label());
    $I->waitForAjaxToFinish();
    $I->canSee($opportunity->label());
  }

  protected function buildTaxonomyTerms(FunctionalTester $I) {
    $parent_1 = $I->createEntity([
      'vid' => 'opportunity_tag_filters',
      'name' => $this->faker->words(2, TRUE),
      'weight' => 0,
    ], 'taxonomy_term');
    $parent_2 = $I->createEntity([
      'vid' => 'opportunity_tag_filters',
      'name' => $this->faker->words(2, TRUE),
      'weight' => 10,
    ], 'taxonomy_term');

    $child_1_1 = $I->createEntity([
      'vid' => 'opportunity_tag_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_1->id(),
    ], 'taxonomy_term');

    $child_1_2 = $I->createEntity([
      'vid' => 'opportunity_tag_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_1->id(),
    ], 'taxonomy_term');

    $child_2_1 = $I->createEntity([
      'vid' => 'opportunity_tag_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_2->id(),
    ], 'taxonomy_term');

    $child_2_2 = $I->createEntity([
      'vid' => 'opportunity_tag_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $child_2_1->id(),
    ], 'taxonomy_term');

    return [
      $parent_1,
      $parent_2,
      $child_1_1,
      $child_2_1,
      $child_1_2,
      $child_2_2,
    ];
  }

}
