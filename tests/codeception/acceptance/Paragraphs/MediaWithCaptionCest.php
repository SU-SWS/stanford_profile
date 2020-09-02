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
    $I->canSee('This is a super caption');
  }

}
