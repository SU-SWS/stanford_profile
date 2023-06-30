<?php
  use Faker\Factory;

/**
 * Class SpacerCest.
 *
 * @group paragraphs
 * @group spacer
 */
class SpacerCest {

  /**
  * Faker service.
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
   * The spacer paragraph has one custom field, to set the size of the bottom margin.
   */
  public function testSpacerParagraph(FunctionalTester $I) {

    $paragraph = $I->createEntity([
      'type' => 'stanford_spacer',
    ], 'paragraph');

    $page = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->logInWithRole('contributor');
    $I->amOnPage($page->toUrl()->toString());
    $I->seeElementInDOM('.paragraph--type--stanford-spacer');
    $I->amOnPage($page->toUrl('edit-form')->toString());
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('Spacer Size');
    $I->selectOption('form select[name=su_spacer_size]', 'Reduced');
    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->seeElementInDOM('.su-spacer-reduced');

    $I->amOnPage($page->toUrl('edit-form')->toString());
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('Spacer Size');
    $I->selectOption('form select[name=su_spacer_size]', 'Minimal');
    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->seeElementInDOM('.su-spacer-minimal');

  }

}
