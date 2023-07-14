<?php

use Faker\Factory;

/**
 * Tests on the mission statement paragraph type.
 *
 * @group mission_statement
 */
class MissionStatementCest {

  /**
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * The paragraph should display on a basic page.
   */
  public function testMissionStatement(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $paragraph = $I->createEntity([
      'type' => 'cardinal_mission_statement',
      'su_mission_text' => 'This is the mission statement whether you choose to accept it or not.',
      'su_mission_cta' => [
        'uri' => 'http://google.com',
        'title' => 'Verify your identity',
      ],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');
    $I->canSee('This is the mission statement whether you choose to accept it or not');
    $I->canSeeLink('Verify your identity', 'http://google.com');
  }

}
