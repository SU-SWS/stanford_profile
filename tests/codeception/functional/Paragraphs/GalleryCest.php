<?php

use Faker\Factory;

/**
 * Class GalleryCest.
 *
 * @group paragraph
 */
class GalleryCest {

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
   * Create a basic page with a gallery and check the colorbox actions.
   */
  public function testGallery(FunctionalTester $I) {

    $I->logInWithRole('contributor');

    $node = $this->getNode($I);
    $I->amOnPage($node->toUrl('edit-form')->toString());

    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('No media items are selected');
    $I->wait(1);
    $I->click('Add media', '.field--name-su-gallery-images');
    $I->waitForText('Drop files here');
    $I->wait(1);
    $I->dropFileInDropzone(__DIR__ . '/logo.jpg');
    $I->dropFileInDropzone(__DIR__ . '/wordmark.jpg');
    $I->click('Upload and Continue');

    $I->waitForText('The media items have been created but have not yet been saved');
    $I->clickWithLeftButton('input[name="media[0][fields][su_gallery_image][0][alt]"]');
    $I->fillField('media[0][fields][su_gallery_image][0][alt]', 'Logo');
    $I->clickWithLeftButton('input[name="media[1][fields][su_gallery_image][0][alt]"]');
    $I->fillField('media[1][fields][su_gallery_image][0][alt]', 'Wordmark');

    $I->wait(1);
    $I->click('Save and insert', '.media-library-widget-modal .ui-dialog-buttonset');

    $I->waitForElementNotVisible('#drupal-modal');
    $I->waitForAjaxToFinish();
    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');

    // On the node page.
    $I->canSee($node->label(), 'h1');
    $I->canSeeNumberOfElements('.stanford-gallery-images img', 2);
    $I->canSeeNumberOfElements('.colorbox', 2);
    $I->click('a.colorbox');
    $I->waitForElementVisible('#cboxLoadedContent');
    $I->canSeeNumberOfElements('#cboxContent img', 1);

    // Go to the next image and make sure its different sources.
    $first_image_src = $I->grabAttributeFrom('#cboxContent img', 'src');
    $I->click('Next', '#cboxContent');
    $I->waitForText('Image 2');
    $second_image_src = $I->grabAttributeFrom('#cboxContent img', 'src');
    $I->assertNotEquals($first_image_src, $second_image_src);
  }

  protected function getNode(FunctionalTester $I){
    $paragraph = $I->createEntity([
      'type' => 'stanford_gallery',
    ], 'paragraph');
    return $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);
  }

}
