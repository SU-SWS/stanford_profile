<?php

use Faker\Factory;

/**
 * Media content tests.
 */
// phpcs:ignore DrupalPractice.General.ClassName.ClassPrefix
class AudioVisualContentCest {

  /**
   * Faker service.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Test the audio/visual duration and transcript link display.
   */
  public function testAudioVisual(AcceptanceTester $I) {
    copy(__DIR__ . '/../assets/test.srt', codecept_data_dir() . 'test.srt');

    $video = $I->createEntity([
      'field_media_oembed_video' => 'https://www.youtube.com/watch?v=XiZTchwa884',
      'bundle' => 'video',
    ], 'media');
    $node = $I->createEntity([
      'title' => $this->faker->uuid(),
      'type' => 'stanford_media',
      'su_media_audio_video' => ['target_id' => $video->id()],
    ]);

    $I->logInWithRole('contributor');
    $I->amOnPage($node->toUrl('edit-form')->toString());

    $I->fillField('Hours', 2);
    $I->fillField('Minutes', 25);

    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSee('Duration 2:25:00');
    $I->cantSeeLink('Read Transcript');

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->attachFile('files[su_media_subtitles_0]', 'test.srt');
    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSeeLink('Read Transcript');
    $I->click('Read Transcript');
    $I->canSeeInCurrentUrl('/printable/print');
    $I->canSee('This is a demonstration of SRT subtitles.');
  }

}
