<?php

use Faker\Factory;

/**
 * Class AccordionCest.
 *
 * @group paragraphs
 */
abstract class AccordionCest {

  /**
   * Create and check the accordion.
   */
  public function testCreatingAccordion(FunctionalTester $I) {
    $faker = Factory::create();

    $paragraph = $I->createEntity([
      'type' => 'stanford_accordion',
      'su_accordion_body' => [
        'value' => 'I can see it in your smile.',
        'format' => 'stanford_minimal_html',
      ],
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

    $I->logInWithRole('contributor');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '.inner-row-wrapper');
    $I->waitForText('Title/Question');
    $I->fillField('Title/Question', 'Hello. Is it me you\'re looking for?');

    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->click('Save');

    $I->canSee('Hello. Is it me you\'re looking for?');
    $I->cantSeeElement('.open');
    $open = $I->grabAttributeFrom('details', 'open');
    $I->assertNull($open);
    $I->clickWithLeftButton('summary');
    $open = $I->grabAttributeFrom('details', 'open');
    $I->assertNotNull($open);
    $I->canSee('I can see it in your smile.');
  }

}
