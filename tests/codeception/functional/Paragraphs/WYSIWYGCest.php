<?php

use Faker\Factory;

/**
 * Class WYSIWYGCest.
 *
 * @group paragraphs
 * @group wysiwyg
 */
class WYSIWYGCest {

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
   * HTML should be properly stripped.
   */
  public function testFilteredHtml(FunctionalTester $I) {
    $node = $this->getNodeWithParagraph($I, file_get_contents(__DIR__ . '/WYSIWYG.html'));
    $I->logInWithRole('administrator');
    $I->amOnPage($node->toUrl()->toString());

    # Stripped Tags
    $I->cantSee("alert('testme')");

    $I->cantSeeElement('.system-main-block iframe');
    $I->cantSeeElement('.system-main-block form');
    $I->cantSeeElement('.system-main-block label');
    $I->cantSeeElement('.system-main-block input');
    $I->cantSeeElement('.system-main-block select');
    $I->cantSeeElement('.system-main-block option');
    $I->cantSeeElement('.system-main-block textarea');
    $I->cantSeeElement('.system-main-block fieldset');
    $I->cantSeeElement('.system-main-block legend');
    $I->cantSeeElement('.system-main-block address');

    # Headers
    $I->cantSee('Level 01 heading', 'h1');
    $I->canSee('Level 02 Heading', 'h2');
    $I->canSee('Level 03 Heading', 'h3');
    $I->canSee('Level 04 Heading', 'h4');
    $I->canSee('Level 05 Heading', 'h5');
    $I->cantSeeElement('h6');


    # Text Tags
    $I->canSee('A small paragraph', 'p');
    $I->canSee('Normal Link', 'a');
    $I->canSee('Button', 'a.su-button');
    $I->canSee('Big Button', 'a.su-button--big');
    $I->canSee('Secondary Button', 'a.su-button--secondary');
    $I->canSee('emphasis', 'em');
    $I->canSee('important', 'strong');
    $I->canSeeNumberOfElements('blockquote', 1);
    $I->cantSeeElement('.su-page-components footer');
    $I->canSeeNumberOfElements('code', 2);
    $I->canSeeNumberOfElements('dl', 1);
    $I->canSeeNumberOfElements('dt', 2);
    $I->canSeeNumberOfElements('dd', 2);

    # List Tags
    $I->canSee('This is a list', 'ul li');
    $I->canSee('child list items', 'ul ul li');
    $I->canSee('Ordered list item', 'ol li');
    $I->canSee('Child ordered list item', 'ol ol li');

    # Table Tags
    $I->canSeeElement('table');
    $I->canSeeNumberOfElements('caption', 1);
    $I->canSeeNumberOfElements('caption', 1);
    $I->canSeeNumberOfElements('tbody', 1);
    $I->canSeeNumberOfElements('tr', 3);
    $I->canSeeNumberOfElements('th[scope]', 2);
    $I->canSeeNumberOfElements('td', 4);
  }

  /**
   * The wysiwyg buttons should work as expected at all times.
   */
  public function testWysiwygButtons(FunctionalTester $I) {
    $node = $this->getNodeWithParagraph($I, 'Lorem Ipsum');
    $I->logInWithRole('contributor');
    $I->resizeWindow(1700, 1000);
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForElementVisible('.ck-toolbar');

    // Wait a second for any click events to be applied.
    $I->wait(1);

    $I->click('[data-cke-tooltip-text="Insert table"]');
    $I->click('[data-row="5"][data-column="3"]');

    $I->click('[data-cke-tooltip-text="Link (Ctrl+K)"]');
    $url = $this->faker->url;
    $I->fillField('Link URL', $url);
    $I->click('[data-cke-tooltip-text="Save"]');
    $I->clickWithLeftButton('.ui-dialog-title');

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->canSeeLink($url);

    $I->canSeeNumberOfElements('.su-wysiwyg-text td', 15);
    $I->canSeeNumberOfElements('.su-wysiwyg-text tr', 5);
  }

  /**
   * Images in the WYSIWYG should display correctly.
   */
  public function testEmbeddedImage(FunctionalTester $I) {
    $node = $this->getNodeWithParagraph($I, 'Lorem Ipsum');
    $I->logInWithRole('administrator');
    $I->resizeWindow(1700, 1000);
    $I->amOnPage($node->toUrl()->toString());
    $I->cantSeeElement('.su-wysiwyg-text img');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForElementVisible('.ck-toolbar');

    // Wait a second for any click events to be applied.
    $I->wait(1);
    $I->click('[data-cke-tooltip-text="Insert Media"]');
    $I->waitForElementVisible('.dropzone');
    $I->dropFileInDropzone(__DIR__ . '/logo.jpg');
    $I->click('Upload and Continue');
    $I->waitForText('Decorative Image');
    $I->click('Save and insert', '.media-library-widget-modal .ui-dialog-buttonset');
    $I->waitForElementNotVisible('.media-library-widget-modal');
    $I->wait(1);

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForAjaxToFinish();
    $I->click('Save');
    $I->canSee($node->label(), 'h1');
    $I->canSeeElement('.su-wysiwyg-text img[src*="logo"]');
  }

  /**
   * Test media category taxonomy field.
   */
  public function testImageCategory(FunctionalTester $I){
    $node = $this->getNodeWithParagraph($I);

    /** @var \Drupal\Core\File\FileSystemInterface $file_system */
    $file_system = \Drupal::service('file_system');
    $image_path = $file_system->copy(__DIR__ . '/logo.jpg', 'public://' . $this->faker->word . '.jpg');
    $file = $I->createEntity(['uri' => $image_path], 'file');

    $unrelated_term = $I->createEntity([
      'vid' => 'media_tags',
      'name' => $this->faker->word,
    ], 'taxonomy_term');

    $parent_term = $I->createEntity([
      'vid' => 'media_tags',
      'name' => $this->faker->word,
    ], 'taxonomy_term');
    $child_term = $I->createEntity([
      'vid' => 'media_tags',
      'name' => $this->faker->word,
      'parent' => $parent_term->id(),
    ], 'taxonomy_term');

    $media = $I->createEntity([
      'bundle' => 'image',
      'field_media_image' => $file->id(),
      'name' => $this->faker->words(3, TRUE),
      'su_media_category' => $child_term,
    ], 'media');

    $I->logInWithRole('site_manager');
    $I->resizeWindow(1700, 1000);
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForElementVisible('.ck-toolbar');

    // Wait a second for any click events to be applied.
    $I->wait(1);
    $I->click('[data-cke-tooltip-text="Insert Media"]');
    $I->waitForElementVisible('.dropzone');

    $I->selectOption('Category', $unrelated_term->label());
    $I->click('Apply filters');
    $I->waitForAjaxToFinish();
    $I->cantSee($media->label());

    $I->selectOption('Category', $parent_term->label());
    $I->click('Apply filters');
    $I->waitForAjaxToFinish();
    $I->canSee($media->label());

    $I->selectOption('Category', $unrelated_term->label());
    $I->click('Apply filters');
    $I->waitForAjaxToFinish();
    $I->cantSee($media->label());

    $I->selectOption('Category', '-'. $child_term->label());
    $I->click('Apply filters');
    $I->waitForAjaxToFinish();
    $I->canSee($media->label());
  }

  /**
   * Videos in the WYSIWYG should display correctly.
   */
  public function testEmbeddedVideo(FunctionalTester $I) {
    $node = $this->getNodeWithParagraph($I, 'Lorem Ipsum');
    $I->logInWithRole('administrator');
    $I->resizeWindow(1700, 1000);
    $I->amOnPage($node->toUrl()->toString());
    $I->cantSeeElement('iframe');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForElementVisible('.ck-toolbar');

    // Wait a second for any click events to be applied.
    $I->wait(1);
    $I->click('[data-cke-tooltip-text="Insert Media"]');
    $I->waitForElementVisible('.dropzone');
    $I->click('Video', '.media-library-menu-video');
    $I->waitForElementVisible('.media-library-add-form-oembed-url');
    $I->clickWithLeftButton('input.media-library-add-form-oembed-url[name="url"]');
    $I->fillField('Add Video via URL', 'https://www.youtube.com/watch?v=ktCgVopf7D0');
    $I->click('Add');

    $I->waitForText('The media item has been created but has not yet been saved');
    $I->fillField('Name', 'Test Youtube Video');
    $I->click('Save and insert', '.media-library-widget-modal .ui-dialog-buttonset');
    $I->waitForElementNotVisible('.media-library-widget-modal');
    $I->wait(1);

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->scrollTo('.oembed-lazyload', 0, 100);
    $I->waitForElementVisible('iframe');
    $I->canSeeNumberOfElements('iframe', 1);
  }

  /**
   * Documents in the WYSIWYG should display correctly.
   */
  public function testEmbeddedDocument(FunctionalTester $I) {
    $node = $this->getNodeWithParagraph($I, 'Lorem Ipsum');
    $I->logInWithRole('administrator');
    $I->resizeWindow(1700, 1000);
    $I->amOnPage($node->toUrl()->toString());
    $I->cantSeeElement('.su-wysiwyg-text a');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForElementVisible('.ck-toolbar');

    // Wait a second for any click events to be applied.
    $I->wait(1);
    $I->click('[data-cke-tooltip-text="Insert Media"]');
    $I->waitForElementVisible('.dropzone');
    $I->click('File', '.media-library-menu-file');
    $I->waitForText('txt, rtf, doc, docx');
    $I->dropFileInDropzone(__FILE__);
    $I->canSeeElement('.dz-error.dz-complete');
    $I->click('.dropzonejs-remove-icon');
    $I->dropFileInDropzone(__DIR__ . '/test.txt');
    $I->click('Upload and Continue');
    $I->waitForText('The media item has been created but has not yet been saved');
    $I->wait(1);
    $I->click('Save and insert', '.media-library-widget-modal .ui-dialog-buttonset');
    $I->waitForElementNotVisible('.media-library-widget-modal');
    $I->wait(1);
    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->canSeeElement('.su-wysiwyg-text a[href*="test"]');
  }

  /**
   * Get a node with a wysiwyg paragraph on it.
   *
   * @param \FunctionalTester $I
   *   Tester.
   * @param string $paragraph_text
   *   String to populate the paragraph.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Node entity.
   */
  protected function getNodeWithParagraph(FunctionalTester $I, $paragraph_text = '') {
    $faker = Factory::create();
    $paragraph = $I->createEntity([
      'type' => 'stanford_wysiwyg',
      'su_wysiwyg_text' => [
        'format' => 'stanford_html',
        'value' => $paragraph_text,
      ],
    ], 'paragraph');

    return $I->createEntity([
      'type' => 'stanford_page',
      'title' => $faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
  }

}
