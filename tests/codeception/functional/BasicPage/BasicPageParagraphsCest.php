<?php

use Faker\Factory;

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
    $I->fillField('URL', '/about');
    $I->fillField('Link text', 'Google Link');
    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->wait(1);
    $I->click('Save', '#edit-actions');
    $I->canSee('Superhead text');
    $I->canSee('Headline');
    $I->canSeeLink('Google Link', '/about');
  }

  /**
   * The user should be able to see all revisions of a node.
   */
  public function testViewRevisions(FunctionalTester $I) {
    $faker = Factory::create();
    $paragraph = $I->createEntity([
      'type' => 'stanford_card',
      'su_card_super_header' => 'Foo Bar',
    ], 'paragraph');

    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ], 'paragraph_row');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $faker->text(30),
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);

    $I->logInWithRole('site_manager');
    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.diff-revisions tbody tr', 1);

    $I->amOnPage("/node/{$node->id()}/edit");
    $I->fillField('Title', $faker->text(15));
    $I->click('Save');
    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.diff-revisions tbody tr', 2);

    $I->amOnPage("/node/{$node->id()}/edit");
    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '.inner-row-wrapper');
    $I->waitForText('Superhead');
    $I->fillField('Superhead', $faker->text(10));
    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->click('Save');

    $I->amOnPage("/node/{$node->id()}/revisions");
    $I->canSeeNumberOfElements('.diff-revisions tbody tr', 3);
  }

}
