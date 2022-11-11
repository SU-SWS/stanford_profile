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
    $field_values =[
      'sup_header' => $this->faker->words(3, true),
      'header' => $this->faker->words(3, true),
      'body' => $this->faker->words(3, true),
      'uri' => $this->faker->url,
      'title' => $this->faker->words(3, true),
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

    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ], 'paragraph_row');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(4, TRUE),
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($field_values['sup_header']);
    $I->canSee($field_values['header']);
    $I->canSee($field_values['body']);
    $I->canSeeLink($field_values['title'], $field_values['uri']);

    $I->cantSeeElement('.overlay-right');

    $I->logInWithRole('site_manager');

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '.inner-row-wrapper');
    $I->waitForText('Style');
    $I->click('Style');
    $I->waitForText('Text Overlay Position');

    $I->clickWithLeftButton('#overlay_position');
    $I->wait(1);
    $I->clickWithLeftButton('li[data-value="right"]');

    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->click('Save');
    $I->canSeeElement('.overlay-right');
  }

}
