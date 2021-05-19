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
   * The banner paragraph should display its fields.
   */
  public function testBannerBehaviors(FunctionalTester $I) {
    $faker = Factory::create();

    $paragraph = $I->createEntity([
      'type' => 'stanford_banner',
      'su_banner_sup_header' => 'This is a super headline',
      'su_banner_header' => 'Some Headline Here',
      'su_banner_button' => [
        'uri' => 'http://google.com/',
        'title' => 'Google Button',
      ],
      'su_banner_body' => 'Ipsum Lorem',
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
      'title' => $faker->text(30),
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee('This is a super headline');
    $I->canSee('Some Headline Here');
    $I->canSee('Ipsum Lorem');
    $I->canSeeLink('Google Button', 'http://google.com/');
    $I->cantSeeElement('.overlay-right');

    $I->logInWithRole('site_manager');

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '.inner-row-wrapper');
    $I->waitForText('Style');
    $I->click('Style');
    $I->waitForText('Text Overlay Position');

    $I->clickWithLeftButton('#overlay_position');
    $I->clickWithLeftButton('li[data-value="right"]');

    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->click('Save');
    $I->canSeeElement('.overlay-right');
  }

}
