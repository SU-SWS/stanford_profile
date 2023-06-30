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

    $news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $I->logInWithRole('contributor');

    $node = $this->getNodeWithReferenceParagraph($I);

    $I->amOnPage($node->toUrl('edit-form')->toString());

    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');

    $I->waitForText('Content Item(s)');
    $I->fillField('[name="su_entity_item[0][target_id]"]', $news->label() . ' (' .$news->id(). ')');

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->canSee('has been updated');
    $I->canSee($news->label(), '.su-card.su-news-vertical-teaser');
  }

  /**
   * Publications can be referenced in teaser paragraph.
   */
  public function testPublicationTeasers(FunctionalTester $I) {
    $publication = $I->createEntity([
      'type' => 'stanford_publication',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $I->logInWithRole('site_manager');
    $I->amOnPage($publication->toUrl('edit-form')->toString());

    $I->selectOption('su_publication_citation[actions][bundle]', 'Journal Article');
    $I->click('Add Citation');
    $I->waitForText('First Name');
    $I->click('Save');
    $I->canSee($publication->label(), 'h1');

    $node = $this->getNodeWithReferenceParagraph($I);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($this->fieldValues['headliner']);
    $I->canSee($this->fieldValues['description']);
    $I->canSeeLink($this->fieldValues['title'], $this->fieldValues['uri']);

    $I->amOnPage("/node/{$node->id()}/edit");
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');

    $I->waitForText('Content Item(s)');
    $I->fillField('[name="su_entity_item[0][target_id]"]', $publication->label() . ' (' .$publication->id(). ')');

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');
    $I->canSee('has been updated');
    $I->canSee($publication->label(), 'h2');
    $I->canSee('Journal Article');
  }

  /**
   * Get a node with an Entity Reference paragraph in a row.
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
