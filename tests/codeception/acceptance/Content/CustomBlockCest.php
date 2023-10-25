<?php

/**
 * Test for custom block types.
 *
 * @group block
 */
class CustomBlockCest {

  /**
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor
   */
  public function __construct() {
    $this->faker = \Faker\Factory::create();
  }

  /**
   * Site managers should be able to edit custom blocks.
   */
  public function testCustomBlockAccess(AcceptanceTester $I) {
    $block = $I->createEntity([
      'type' => 'stanford_component_block',
      'info' => $this->faker->word(3, TRUE),
    ], 'block_content');
    $I->logInWithRole('site_manager');
    $I->amOnPage($block->toUrl()->toString());
    $I->fillField('Block description', 'Foo Bar');
    $I->click('Save');
    $I->canSee('has been updated');
  }

}
