<?php

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;

/**
 * Test Person content type.
 */
#[CodeceptionAttribute\Group('person')]
class PersonCest {

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

  public function testContentType(FunctionalTester $I) {
    [
      $parent_1,
      $parent_2,
      $child_1_1,
      $child_2_1,
      $child_1_2,
      $child_2_2,
    ] = $this->buildTaxonomyTerms($I);

    $node = $I->createEntity([
      'type' => 'stanford_person',
      'title' => $this->faker->words(3, TRUE),
      'su_person_first_name' => $this->faker->firstName(),
      'su_person_last_name' => $this->faker->lastName(),
    ]);

    $I->logInWithRole('contributor');

    $I->amOnPage($node->toUrl('edit-form')->toString());

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
    $I->canSeeInCurrentUrl($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
  }


  #[CodeceptionAttribute\Group('people-filters')]
  public function testFilteringPeople(FunctionalTester $I) {
    [
      $parent_1,
      $parent_2,
      $child_1_1,
      $child_2_1,
      $child_1_2,
      $child_2_2,
    ] = $this->buildTaxonomyTerms($I);
    $person = $I->createEntity([
      'type' => 'stanford_person',
      'title' => $this->faker->words(3, TRUE),
      'su_person_first_name' => $this->faker->firstName(),
      'su_person_last_name' => $this->faker->lastName(),
      'su_opp_tags' => [
        ['target_id' => $child_1_1->id()],
        ['target_id' => $child_2_1->id()],
      ],
    ]);
    $paragraph = $I->createEntity([
      'type' => 'stanford_filtered_lists',
      'su_list_headline' => $this->faker->words(3, TRUE),
      'su_filtered_list_view' => [
        'target_id' => 'people_filtered',
        'display_id' => 'grid_list_all',
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
    $I->canSee($person->label());

    $I->waitForText($parent_1->label(), 2, 'fieldset');

    $I->checkOption($child_1_2->label());
    $I->waitForAjaxToFinish();
    $I->cantSee($person->label());

    $I->checkOption($child_1_1->label());
    $I->waitForAjaxToFinish();
    $I->canSee($person->label());
  }

  protected function buildTaxonomyTerms(FunctionalTester $I) {
    $parent_1 = $I->createEntity([
      'vid' => 'person_filters',
      'name' => $this->faker->words(2, TRUE),
      'weight' => 0,
    ], 'taxonomy_term');
    $parent_2 = $I->createEntity([
      'vid' => 'person_filters',
      'name' => $this->faker->words(2, TRUE),
      'weight' => 10,
    ], 'taxonomy_term');

    $child_1_1 = $I->createEntity([
      'vid' => 'person_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_1->id(),
    ], 'taxonomy_term');

    $child_1_2 = $I->createEntity([
      'vid' => 'person_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_1->id(),
    ], 'taxonomy_term');

    $child_2_1 = $I->createEntity([
      'vid' => 'person_filters',
      'name' => $this->faker->words(2, TRUE),
      'parent' => $parent_2->id(),
    ], 'taxonomy_term');

    $child_2_2 = $I->createEntity([
      'vid' => 'person_filters',
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
