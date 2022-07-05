<?php

use Faker\Factory;

/**
 * Class GalleryCest.
 *
 * @group paragraph
 */
class GalleryCest {

  /**
   * Create a basic page with a gallery and check the colorbox actions.
   */
  public function testGallery(FunctionalTester $I) {
    $faker = Factory::create();
    $title = $faker->text;
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_page');

    // Create the node.
    $I->fillField('Title', $title);
    $I->doubleClick('#tool-stanford_gallery');
    $I->waitForText('Images', 10, '.MuiDialog-container');
    $I->click('Add media', '.MuiDialog-container');
    $I->waitForText('Drop files here to upload them');

    $I->dropFileInDropzone(__DIR__ . '/logo.jpg');
    $I->dropFileInDropzone(__DIR__ . '/wordmark.jpg');
    $I->click('Upload and Continue');

    $I->waitForText('The media items have been created but have not yet been saved');
    $I->clickWithLeftButton('input[name="media[0][fields][su_gallery_image][0][alt]"]');
    $I->fillField('media[0][fields][su_gallery_image][0][alt]', 'Logo');
    $I->clickWithLeftButton('input[name="media[1][fields][su_gallery_image][0][alt]"]');
    $I->fillField('media[1][fields][su_gallery_image][0][alt]', 'Wordmark');
    $I->click('Save and insert', '.ui-dialog-buttonset');

    $I->waitForElementNotVisible('#drupal-modal');
    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-container');
    $I->click('Save');

    // On the node page.
    $I->canSee($title, 'h1');
    $I->canSeeNumberOfElements('.paragraph-item img', 2);
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

}
