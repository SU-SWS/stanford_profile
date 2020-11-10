<?php

/**
 * Test for the basic page content type.
 */
class BasicPageParagraphsCest {

  /**
   * Test the card component data is displayed correctly.
   */
  public function testCardParagraph(FunctionalTester $I) {
    $paragraph = $I->createEntity(['type' => 'stanford_card'], 'paragraph');

    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ], 'paragraph_row');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'Test Cards',
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);
    $I->logInWithRole('contributor');
    $I->amOnPage("/node/{$node->id()}/edit");
    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '#row-0');
    $I->waitForText('Superhead');
    $I->fillField('Superhead', 'Superhead text');
    $I->fillField('Headline', 'Headline');
    $I->fillField('URL', 'http://google.com');
    $I->fillField('Link text', 'Google Link');
    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->wait(1);
    $I->click('Save', '#edit-actions');
    $I->canSee('Superhead text');
    $I->canSee('Headline');
    $I->canSeeLink('Google Link', 'http://google.com/');
  }

  /**
   * If a user searches in the media library, cardinality is checked.
   */
  public function testMultipleImagesD8Core2428(FunctionalTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_page');
    // Give it a few seconds to apply the media library javscript.
    $I->wait(3);
    $I->click('Add Top Banner');
    $I->waitForText('No media items are selected.');
    $I->click('Add media', 'form');
    $I->waitForText('Add or select media');
    $disabled = $I->grabAttributeFrom('[name="media_library_select_form[1]"]', 'disabled');
    $I->assertFalse((bool)$disabled);

    $I->checkOption('media_library_select_form[0]');
    $disabled = $I->grabAttributeFrom('[name="media_library_select_form[1]"]', 'disabled');
    $I->assertTrue((bool)$disabled);

    $I->fillField('Name', 'banner');
    $I->click('Apply filters');
    $I->waitForAjaxToFinish();

    $I->checkOption('media_library_select_form[0]');
    $I->fillField('Name', '');
    $I->click('Apply filters');
    $I->waitForAjaxToFinish();

    $I->canSee('2 of 1 item selected');
    $I->click('Insert selected', '.ui-dialog-buttonset');
    $I->waitForAjaxToFinish();
    $I->canSeeElement('.messages.messages--error');

    $I->uncheckOption('media_library_select_form[0]');
    $I->click('Insert selected', '.ui-dialog-buttonset');

    $I->waitForAjaxToFinish();
    $I->canSeeNumberOfElements('.media-library-item__preview img', 1);
  }

}
