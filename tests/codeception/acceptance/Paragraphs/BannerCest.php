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
  public function testBanner(AcceptanceTester $I) {
    $faker = Factory::create();

    $paragraph = $I->createEntity([
      'type' => 'stanford_banner',
      'su_banner_sup_header' => 'This is a super headline',
      'su_banner_header' => 'Some Headline Here',
      'su_banner_button' => [
        'uri' => 'http://google.com',
        'title' => 'Google Button',
      ],
      'su_banner_body' => 'Ipsum Lorem',
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
        'settings' => json_encode([
          'row' => 0,
          'index' => 0,
          'width' => 12,
          'admin_title' => 'Banner',
        ]),
      ],
    ]);

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee('This is a super headline');
    $I->canSee('Some Headline Here');
    $I->canSee('Ipsum Lorem');
    $I->canSeeLink('Google Button', 'http://google.com');
  }

}
