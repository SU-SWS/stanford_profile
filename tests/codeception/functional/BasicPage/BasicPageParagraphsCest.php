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
    $I->waitForElement('.MuiAutocomplete-input');
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

}
