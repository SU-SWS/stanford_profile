<?php

namespace Content;

use Codeception\Example;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use FunctionalTester;
use Faker\Factory;
use Codeception\Attribute as CodeceptionAttribute;

#[CodeceptionAttribute\Group('content')]
#[CodeceptionAttribute\Group('media-content')]
#[CodeceptionAttribute\Group('media')]
class MediaCest {

  /**
   * @var \Faker\Generator
   */
  protected $faker;

  public function _before(FunctionalTester $I) {
    $this->faker = Factory::create();
  }

  #[CodeceptionAttribute\Examples(videos: ['https://www.youtube.com/watch?v=WNEyg3UKTVQ'])]
  #[CodeceptionAttribute\Examples(videos: [
    'https://www.youtube.com/watch?v=WNEyg3UKTVQ',
    'https://www.youtube.com/watch?v=myjrQS_7zNk',
  ])]
  public function testAddImageMedia(FunctionalTester $I, Example $example) {
    foreach ($example['videos'] as $videoUrl) {
      $mediaVideos[] = $I->createEntity([
        'bundle' => 'video',
        'field_media_oembed_video' => $videoUrl,
      ], 'media');
    }

    $mediaType = $I->createEntity([
      'vid' => 'media_content_types',
      'name' => $this->faker->words(3, TRUE),
    ], 'taxonomy_term');
    $person = $I->createEntity([
      'type' => 'stanford_person',
      'title' => $this->faker->words(4, TRUE),
      'su_person_first_name' => $this->faker->firstName(),
      'su_person_last_name' => $this->faker->lastName(),
    ]);
    $series = $this->faker->word();
    $otherMedia = $I->createEntity([
      'type' => 'stanford_media',
      'title' => $this->faker->words(4, TRUE),
      'su_media_episode' => $this->faker->word(),
      'su_media_season' => $this->faker->word(),
      'su_media_series' => $series,
    ]);

    /** @var \Drupal\Core\File\FileSystemInterface $fs */
    $fs = \Drupal::service('file_system');
    $transcriptUri = $fs->copy(__DIR__ . '/transcript.srt', 'public://transcript.srt');
    $transcriptFile = $I->createEntity(['uri' => $transcriptUri], 'file');

    $mediaNode = $I->createEntity([
      'type' => 'stanford_media',
      'title' => $this->faker->words(4, TRUE),
      'su_media_audio_video' => array_map(fn($media) => $media->id(), $mediaVideos),
      'su_media_dek' => $this->faker->sentences(1, TRUE),
      'body' => [
        'value' => '<p>' . implode('</p><p>', $this->faker->paragraphs(4)) . '</p>',
        'format' => 'stanford_html',
      ],
      'su_media_episode' => $this->faker->word(),
      'su_media_season' => $this->faker->word(),
      'su_media_series' => $series,
      'su_media_person' => $person->id(),
      'su_media_date' => date(DateTimeItemInterface::DATE_STORAGE_FORMAT),
      'su_media_types' => $mediaType->id(),
      'su_media_subtitles' => $transcriptFile->id(),
    ]);

    $I->amOnPage($mediaNode->toUrl()->toString());
    $I->canSee($mediaNode->label(), 'h1');
    $I->canSee($mediaType->label());
    $I->canSeeLink($person->label(), $person->toUrl()->toString());
    $I->canSeeLink('Read Transcript');
    $I->canSeeElement('iframe[src*="WNEyg3UKTVQ"]');

    $I->canSee('Part of Series');
    $I->canSee($series);

    $I->canSee('Next', 'h2');
    $I->canSee($otherMedia->label(), 'h3');
    $I->canSee('Related Topics', 'h2');

    if (count($mediaVideos) > 1) {
      $I->canSee('Video clips from: ' . $mediaNode->label(), 'h2');
      for ($j = 1; $j < count($mediaVideos); $j++) {
        $I->canSeeLink($mediaVideos[$j]->label());
      }

      $lastVideo = end($mediaVideos);
      $I->click($lastVideo->label());
      $I->waitForText($lastVideo->label(), 10, '.ui-dialog');
      $I->canSeeElement('.ui-dialog iframe');
    }
    else {
      $I->cantSee('Video clips from');
    }

    $I->amOnPage($person->toUrl()->toString());
    $I->canSee($person->label(), 'h1');
    $I->canSee('Media', 'h2');
    $I->canSee($mediaNode->label(), 'h3');
    $I->canSeeLink($mediaNode->label(), $mediaNode->toUrl()->toString());
  }

  /**
   * Test adding SDR to wysiwyg.
   */
  public function testAddSdrMedia(FunctionalTester $I) {
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(2, TRUE),
    ]);
    $I->logInWithRole('site_manager');
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($node->label(), 'h1');

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElement('[data-cke-tooltip-text="Insert Media"]');
    $I->click('Insert Media');

    $I->waitForText('Stanford Digital Repository', 10, '.media-library-menu');
    $I->click('Stanford Digital Repository', '.media-library-menu');
    $I->waitForText('oEmbed URL', 10, '.media-library-add-form');
    $I->fillField('oEmbed URL', 'https://purl.stanford.edu/cd436vr6503');
    $I->click('Add');
    $I->waitForText('The media item has been created but has not yet been saved');
    $I->click('//button[contains(text(), "Save and insert")]');

    $I->waitForElementNotVisible('.media-library-add-form');
    $I->click('Save');

    $I->canSee($node->label(), 'h1');
    $I->canSeeElement('iframe[src*="cd436vr6503"]');
  }

}
