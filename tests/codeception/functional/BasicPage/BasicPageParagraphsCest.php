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
   *
   * @group aria-label
   */
  public function testCardParagraph(FunctionalTester $I) {
    $card_values =[
      'superhead' => $this->faker->words(3, true),
      'headline' => $this->faker->words(3, true),
      'uri' => $this->faker->url,
      'title' => $this->faker->words(3, true),
      'aria-label' => $this->faker->words(5, true),
    ];

    $paragraph = $I->createEntity(['type' => 'stanford_card'], 'paragraph');

    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ], 'paragraph_row');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, true),
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);
    $I->logInWithRole('contributor');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '#row-0');
    $I->waitForText('Superhead');
    $I->fillField('Superhead', $card_values['superhead']);
    $I->fillField('Headline', $card_values['headline']);
    $I->fillField('URL', $card_values['uri']);
    $I->fillField('Link text', $card_values['title']);
    $I->fillField('ARIA Label',$card_values['aria-label']);
    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->wait(1);
    $I->click('Save', '#edit-actions');
    $I->canSee($card_values['superhead']);
    $I->canSee($card_values['headline']);
    $I->canSeeLink($card_values['title'], $card_values['uri']);
    $aria_label = $I->grabAttributeFrom("a[href='{$card_values['uri']}']", 'aria-label');
    $I->assertEquals($card_values['aria-label'], $aria_label, sprintf('Attribute aria-label `%s` does not match expected value `%s`', $aria_label, $card_values['aria-label']));
  }

  /**
   * The user should be able to see all revisions of a node.
   */
  public function testViewRevisions(FunctionalTester $I) {
    $paragraph = $I->createEntity([
      'type' => 'stanford_card',
      'su_card_super_header' => 'Foo Bar',
    ], 'paragraph');

    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ], 'paragraph_row');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
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
    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '.inner-row-wrapper');
    $I->waitForText('Superhead');
    $I->fillField('Superhead', $this->faker->text(10));
    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->click('Save');

    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.diff-revisions tbody tr', 3);
  }

}
