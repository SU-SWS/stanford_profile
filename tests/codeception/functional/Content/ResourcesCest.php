<?php

use Faker\Factory;

/**
 * Class ResourcesCest.
 *
 * @group basic_page
 * @group resources
 */
class ResourcesCest {

  /**
   * Faker generator.
   *
   * @var \Faker\Generator $faker
   */
  protected $faker;

  /**
   * EventsCest constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Create some resource pages and make sure they display on the list.
   */
  public function testResources(FunctionalTester $I) {
    $foo = strtolower($this->faker->word);
    $bar = strtolower($this->faker->word);
    $dimension = $I->createEntity([
      'vid' => 'su_opportunity_dimension',
      'name' => $foo,
    ], 'taxonomy_term');

    $type = $I->createEntity([
      'vid' => 'cs_resource_type',
      'name' => $foo,
    ], 'taxonomy_term');
    $foo_aud = $I->createEntity([
      'vid' => 'cs_resource_audience',
      'name' => $foo,
    ], 'taxonomy_term');
    $bar_aud = $I->createEntity([
      'vid' => 'cs_resource_audience',
      'name' => $bar,
    ], 'taxonomy_term');

   $foo_page = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, true),
      'su_page_resource_audience' => $foo_aud->id(),
      'su_page_resource_type' => $type->id(),
      'su_page_resource_dimension' => $dimension->id(),
    ]);
   $bar_page = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, true),
      'su_page_resource_audience' => $bar_aud->id(),
      'su_page_resource_type' => $type->id(),
      'su_page_resource_dimension' => $dimension->id(),
    ]);

    $paragraph = $I->createEntity([
      'type' => 'stanford_resource_list',
      'su_resource_list' => [
        'target_id' => 'cs_resources',
        'display_id' => 'audience',
        'arguments' => $foo,
      ],
    ], 'paragraph');

    $resources = $I->createEntity([
      'type' => 'stanford_page',
      'title' =>$this->faker->words(3, true),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage($resources->toUrl()->toString());
    $I->canSee($resources->label(), 'h1');
    $I->canSeeLink($foo_page->label());
    $I->cantSeeLink($bar_page->label());

    $paragraph->set('su_resource_list', [
      'target_id' => 'cs_resources',
      'display_id' => 'all_list',
    ])->save();

    $I->logInWithRole('site_manager');
    $I->amOnPage($resources->toUrl('edit-form')->toString());
    $I->click('Save');
    $I->canSeeLink($foo_page->label());
    $I->canSeeLink($bar_page->label());
  }

}
