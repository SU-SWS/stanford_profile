<?php

use Faker\Factory;

/**
 * Class GalleryCest.
 *
 * @group paragraphs
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
    $I->checkOption('media_library_select_form[0]');
    $I->checkOption('media_library_select_form[1]');
    $I->checkOption('media_library_select_form[2]');
    $I->click('Insert selected', '.ui-dialog-buttonset');
    $I->waitForElementNotVisible('#drupal-modal');
    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-container');
    $I->click('Save');

    // On the node page.
    $I->canSee($title, 'h1');
    $I->canSeeNumberOfElements('.paragraph-item img', 3);
    $I->canSeeNumberOfElements('.colorbox', 3);
    $I->click('a.colorbox');
    $I->waitForElementVisible('#cboxLoadedContent');
    $I->canSeeNumberOfElements('#cboxContent img', 1);

    // Go to the next image and make sure its different sources.
    $first_image_src = $I->grabAttributeFrom('#cboxContent img', 'src');
    $I->click('Next', '#cboxContent');
    $I->waitForElementVisible('#cboxLoadedContent');
    $second_image_src = $I->grabAttributeFrom('#cboxContent img', 'src');
    $I->assertNotEquals($first_image_src, $second_image_src);
  }

}
