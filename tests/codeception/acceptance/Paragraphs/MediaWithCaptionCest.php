<?php

use Faker\Factory;

/**
 * Class MediaWithCaptionCest.
 *
 * @group paragraphs
 * @group media_caption
 */
class MediaWithCaptionCest {

  /**
   * A media with caption paragraph will display its fields.
   */
  public function testMediaParagraph(AcceptanceTester $I) {
    $faker = Factory::create();

    $paragraph = $I->createEntity([
      'type' => 'stanford_media_caption',
      'su_media_caption_caption' => 'This is a super caption',
      'su_media_caption_link' => [
        'uri' => 'http://google.com',
        'title' => 'Google Button',
      ],
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
    $I->canSee('This is a super caption');
  }

}
