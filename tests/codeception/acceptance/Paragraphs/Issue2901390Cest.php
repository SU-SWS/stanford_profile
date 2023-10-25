<?php

use Faker\Factory;

/**
 * Class Issue2901390Cest.
 *
 * @group paragraphs
 * @group bug_fix
 *
 * @link https://www.drupal.org/node/2901390
 */
class Issue2901390Cest {

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
   * A user should be able to create a custom block in layout builder.
   */
  public function testLayoutBuilderParagraph(AcceptanceTester $I) {
    $user = $I->createUserWithRoles(['site_manager', 'layout_builder_user']);
    $I->logInAs($user->id());
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(20),
    ]);
    $I->amOnPage($node->toUrl()->toString());
    $I->click('Layout');
    $I->click('Add block');

    $I->click('Create content block');
    $I->fillField('Title', 'Custom Block');
    $I->fillField('Body', 'Lorem Ipsum Custom Block Text');
    $I->click('Add block');
    $I->click('Save layout');
    $I->canSee('Lorem Ipsum Custom Block Text');
  }

}
