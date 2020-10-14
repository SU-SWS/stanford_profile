<?php

use Faker\Factory;

/**
 * Class EntityReferenceCest.
 */
class EntityReferenceCest {

  /**
   * News items should display in the list paragraph.
   */
  public function testEntityReference(FunctionalTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_news');
    $I->fillField('Headline', 'Foo Bar News');
    $I->click('Save');

    $node = $this->getNodeWithList($I);

    $I->amOnPage($node->toUrl()->toString());
    $I->click('Edit', '.local-tasks-block');

    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '.inner-row-wrapper');

    $I->waitForText('Content Items');
    $I->fillField('#su_entity_item', 'Foo Bar News');
    $I->click('.MuiAutocomplete-option');

    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->click('Save');
    $I->canSee('has been updated');
    $I->canSee('Foo Bar News');
  }

  /**
   * Get a node with a Entity Reference paragraph in a row.
   *
   * @param \FunctionalTester $I
   *   Tester.
   *
   * @return bool|\Drupal\node\NodeInterface
   */
  protected function getNodeWithList(FunctionalTester $I) {
    $faker = Factory::create();

    $paragraph = $I->createEntity([
      'type' => 'stanford_entity',
      'su_list_headline' => 'Headliner',
      'su_list_description' => [
        'format' => 'stanford_basic_html',
        'value' => '<p>Lorem Ipsum</p>',
      ],
      'su_list_button' => ['uri' => 'http://google.com', 'title' => 'Google'],
    ], 'paragraph');
    $row = $I->createEntity([
      'type' => 'node_stanford_page_row',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ], 'paragraph_row');

    return $I->createEntity([
      'type' => 'stanford_page',
      'title' => $faker->text(30),
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);
  }

}
