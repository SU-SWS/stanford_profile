<?php

use Faker\Factory;

/**
 * Test for the basic page content type.
 */
class BasicPageParagraphsCest {

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
   * Test the card component data is displayed correctly.
   */
  public function testCardParagraph(FunctionalTester $I) {
    $card_values =[
      'superhead' => $this->faker->words(3, true),
      'headline' => $this->faker->words(3, true),
      'uri' => $this->faker->url,
      'title' => $this->faker->words(3, true),
    ];

    $paragraph = $I->createEntity(['type' => 'stanford_card'], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, true),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
    $I->logInWithRole('contributor');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('Superhead');
    $I->fillField('Superhead', $card_values['superhead']);
    $I->fillField('Headline', $card_values['headline']);
    $I->fillField('URL', $card_values['uri']);
    $I->fillField('Link text', $card_values['title']);
    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->wait(1);
    $I->click('Save', '#edit-actions');
    $I->canSee($card_values['superhead']);
    $I->canSee($card_values['headline']);
    $I->canSeeLink($card_values['title'], $card_values['uri']);
  }

  /**
   * The user should be able to see all revisions of a node.
   */
  public function testViewRevisions(FunctionalTester $I) {
    $paragraph = $I->createEntity([
      'type' => 'stanford_card',
      'su_card_super_header' => 'Foo Bar',
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->logInWithRole('site_manager');
    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.diff-revisions tbody tr', 1);

    $I->amOnPage("/node/{$node->id()}/edit");
    $I->fillField('Title', $this->faker->text(15));
    $I->click('Save');
    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.diff-revisions tbody tr', 2);

    $I->amOnPage("/node/{$node->id()}/edit");
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('Superhead');
    $I->fillField('Superhead', $this->faker->text(10));
    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');

    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.diff-revisions tbody tr', 3);
  }

}
