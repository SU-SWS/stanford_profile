<?php

use Faker\Factory;

/**
 * Class BannerCest.
 *
 * @group paragraphs
 * @group banner
 */
class BannerCest {

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
   * The banner paragraph should display its fields.
   */
  public function testBannerBehaviors(FunctionalTester $I) {
    $field_values = [
      'sup_header' => $this->faker->words(3, TRUE),
      'header' => $this->faker->words(3, TRUE),
      'body' => $this->faker->words(3, TRUE),
      'uri' => $this->faker->url,
      'title' => $this->faker->words(3, TRUE),
    ];

    $paragraph = $I->createEntity([
      'type' => 'stanford_banner',
      'su_banner_sup_header' => $field_values['sup_header'],
      'su_banner_header' => $field_values['header'],
      'su_banner_button' => [
        'uri' => $field_values['uri'],
        'title' => $field_values['title'],
        'options' => [],
      ],
      'su_banner_body' => $field_values['body'],
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(4, TRUE),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($field_values['sup_header']);
    $I->canSee($field_values['header'], 'h2');
    $I->canSee($field_values['body']);
    $I->canSeeLink($field_values['title'], $field_values['uri']);

    $I->cantSeeElement('.overlay-right');

    $I->logInWithRole('site_manager');

    // Overlay position and h3 heading.
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('Behaviors');
    $I->clickWithLeftButton('.lpb-behavior-plugins summary');
    $I->selectOption('Text Overlay Position', 'Right');;
    $I->selectOption('Heading Level', 'h3');

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->canSeeElement('.overlay-right');
    $I->cantSee($field_values['header'], 'h2');
    $I->canSee($field_values['header'], 'h3');

    // H4 heading.
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('Behaviors');
    $I->clickWithLeftButton('.lpb-behavior-plugins summary');
    $I->selectOption('Heading Level', 'h4');

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->cantSee($field_values['header'], 'h2');
    $I->canSee($field_values['header'], 'h4');

    // Splash Text heading.
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('Behaviors');
    $I->clickWithLeftButton('.lpb-behavior-plugins summary');
    $I->selectOption('Heading Level', 'Splash Text');

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->cantSee($field_values['header'], 'h2');
    $I->cantSee($field_values['header'], 'h3');
    $I->cantSee($field_values['header'], 'h4');
    $I->canSee($field_values['header'], 'div.su-font-splash');

    // Visually hidden heading.
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('Behaviors');
    $I->clickWithLeftButton('.lpb-behavior-plugins summary');
    $I->selectOption('Heading Level', 'h2');
    $I->checkOption('Visually Hide Heading');

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->canSee($field_values['header'], 'h2.visually-hidden');
  }

}
