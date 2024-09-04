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

  public function testContentType(FunctionalTester $I) {
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

    $node = $I->createEntity([
      'type' => 'stanford_opportunity',
      'title' => $this->faker->words(3, TRUE),
    ]);

    $user = $I->createUserWithRoles(['site_manager', 'opportunity_editor']);
    $I->logInAs($user->getAccountName());

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->click('#edit-group-basics summary');
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
  }

}
