<?php

use Faker\Factory;

/**
 * Class EntityReferenceCest.
 */
class EntityReferenceCest {

  /**
   * Faker service.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Keyed array of field values.
   *
   * @var array
   */
  protected $fieldValues = [];

  /**
   * Test constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * News items should display in the list paragraph.
   */
  public function testEntityReference(FunctionalTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_news');
    $I->fillField('Headline', 'Foo Bar News');
    $I->click('Save');

    $node = $this->getNodeWithReferenceParagraph($I);

    $I->amOnPage($node->toUrl()->toString());
    $I->click('Edit', '.local-tasks-block');

    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '.inner-row-wrapper');

    $I->waitForText('Content Item(s)');
    $I->fillField('#su_entity_item', 'Foo Bar News');
    $I->click('.MuiAutocomplete-option');

    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->click('Save');
    $I->canSee('has been updated');
    $I->canSee('Foo Bar News', '.su-card.su-news-vertical-teaser');
  }

  /**
   * Publications can be referenced in teaser paragraph.
   */
  public function testPublicationTeasers(FunctionalTester $I) {
    $publication_title = $this->faker->text(20);
    $I->logInWithRole('site_manager');
    $I->amOnPage('node/add/stanford_publication');
    $I->fillField('Title', $publication_title);
    $I->selectOption('su_publication_citation[actions][bundle]', 'Journal Article');
    $I->click('Add Citation');
    $I->waitForText('First Name');
    $I->click('Save');
    $I->canSee($publication_title, 'h1');

    $node = $this->getNodeWithReferenceParagraph($I);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($this->fieldValues['headliner']);
    $I->canSee($this->fieldValues['description']);
    $I->canSeeLink($this->fieldValues['title'], $this->fieldValues['uri']);

    $I->amOnPage("/node/{$node->id()}/edit");
    $I->waitForElementVisible('#row-0');
    $I->click('Edit', '.inner-row-wrapper');

    $I->waitForText('Content Item(s)');
    $I->fillField('#su_entity_item', $publication_title);
    $I->click('.MuiAutocomplete-option');

    $I->click('Continue');
    $I->waitForElementNotVisible('.MuiDialog-scrollPaper');
    $I->click('Save');
    $I->canSee('has been updated');
    $I->canSee($publication_title, 'h2');
    $I->canSee('Journal Article');
  }

  /**
   * Get a node with a Entity Reference paragraph in a row.
   *
   * @param \FunctionalTester $I
   *   Tester.
   *
   * @return bool|\Drupal\node\NodeInterface
   */
  protected function getNodeWithReferenceParagraph(FunctionalTester $I) {
    $this->fieldValues = [
      'headliner' => $this->faker->words(3, TRUE),
      'description' => $this->faker->words(3, TRUE),
      'uri' => $this->faker->url,
      'title' => $this->faker->words(3, TRUE),
    ];

    $paragraph = $I->createEntity([
      'type' => 'stanford_entity',
      'su_entity_headline' => $this->fieldValues['headliner'],
      'su_entity_description' => [
        'format' => 'stanford_html',
        'value' => $this->fieldValues['description'],
      ],
      'su_entity_button' => [
        'uri' => $this->fieldValues['uri'],
        'title' => $this->fieldValues['title'],
        'options' => [],
      ],
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
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $row->id(),
        'entity' => $row,
      ],
    ]);
  }

}
