<?php

use Faker\Factory;

class LinkItCest {

  /**
   * @var \Faker\Factory
   */
  protected $faker;

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  public function testLinkingMedia(FunctionalTester $I) {
    $fileName = strtolower($this->faker->word());
    /** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
    $fileSystem = \Drupal::service('file_system');
    $filePath = $fileSystem->saveData($this->faker->paragraphs(5), "public://$fileName.txt");

    $file = $I->createEntity(['uri' => $filePath], 'file');
    $fileMedia = $I->createEntity([
      'bundle' => 'file',
      'name' => $fileName,
      'field_media_file' => ['target_id' => $file->id()],
    ], 'media');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(3, TRUE),
      'body' => [
        'value' => '<p class="test-me">' . $this->faker->words(3, TRUE) . '</p>',
        'format' => 'stanford_html',
      ],
    ]);
    $I->logInWithRole('contributor');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->clickWithLeftButton('.test-me');
    $I->pressKey('.test-me', ['ctrl', 'k']);
    $I->canSee('Link URL', '.ck-balloon-panel');
    $I->fillField('Link URL', $fileName);
    $I->waitForText("$fileName.txt");
    $I->clickWithLeftButton(".linkit-result-line--title:contains('$fileName')");
    $I->click('Insert');
    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSeeLink($fileName);
    $linkUrl = $I->grabAttributeFrom('.test-me a', 'href');
    $I->assertStringContainsString("/$fileName.txt", $linkUrl);
  }

}
