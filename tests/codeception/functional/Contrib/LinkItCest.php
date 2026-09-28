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
    $docName = preg_replace('/[^a-z0-9]/', '-', strtolower($this->faker->words(3, TRUE)));
    $imageName = preg_replace('/[^a-z0-9]/', '-', strtolower($this->faker->words(3, TRUE)));

    /** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
    $fileSystem = \Drupal::service('file_system');

    $imagePath = $fileSystem->copy(__DIR__ . '/../Paragraphs/logo.jpg', "public://$imageName.jpg");
    $image = $I->createEntity(['uri' => $imagePath], 'file');
    $imageMedia = $I->createEntity([
      'bundle' => 'image',
      'name' => $imageName,
      'field_media_image' => $image->id(),
    ], 'media');

    $docPath = $fileSystem->saveData($this->faker->paragraphs(5), "public://$docName.txt");

    $doc = $I->createEntity(['uri' => $docPath], 'file');
    $I->createEntity([
      'bundle' => 'file',
      'name' => $docName,
      'field_media_file' => $doc->id(),
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
    $I->fillField('Link URL', $docName);
    $I->waitForText("$docName.txt");
    $I->clickWithLeftButton('.linkit-result-line--title');
    $I->click('Insert');
    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSeeLink($docName);
    $linkUrl = $I->grabAttributeFrom('.test-me a', 'href');
    $I->assertStringContainsString("/$docName.txt", $linkUrl);

    $node->set('body', [
      'value' => '<p class="test-me"></p>',
      'format' => 'stanford_html',
    ])->save();
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->clickWithLeftButton('.test-me');
    $I->click('[data-cke-tooltip-text="Insert Media"]');
    $I->waitForText('Add or select media');
    $I->checkOption('[name="media_library_select_form[' . $imageMedia->id() . ']"]');
    $I->click('//button[contains(text(), "Insert selected")]');
    $I->waitForElementNotVisible('.media-library-widget-modal');
    $I->waitForElementVisible('figure.drupal-media');
    $I->clickWithLeftButton('.ck-editor__editable');
    $I->pressKey('.ck-editor__editable', ['ctrl', 'k']);
    $I->canSee('Link URL', '.ck-balloon-panel');
    $I->fillField('Link URL', $docName);
    $I->waitForText("$docName.txt");
    $I->clickWithLeftButton('.linkit-result-line--title');
    $I->click('Insert');
    $I->click('Save');

    $I->canSee($node->label(), 'h1');
    $I->canSeeElement('.su-wysiwyg-text a[href] img[src*="' . $imageName . '"]');
    $linkUrl = $I->grabAttributeFrom('.su-wysiwyg-text a', 'href');
    $I->assertStringContainsString("/$docName.txt", $linkUrl);
  }

}
