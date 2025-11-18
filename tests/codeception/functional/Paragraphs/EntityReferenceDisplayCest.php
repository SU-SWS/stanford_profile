<?php

use Codeception\Attribute as CodeceptionAttribute;
use Codeception\Example;
use Faker\Factory;

/**
 * Tests Entity Reference Display module integration with stanford_entity paragraph.
 */
#[CodeceptionAttribute\Group('paragraphs')]
#[CodeceptionAttribute\Group('entity_reference_display')]
#[CodeceptionAttribute\Group('news')]
class EntityReferenceDisplayCest {

  /**
   * @var \Faker\Generator
   */
  protected $faker;

  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Test adding multiple news items.
   */
  public function testMultipleNewsItems(FunctionalTester $I) {
    $news_items = [];
    for ($i = 0; $i < 3; $i++) {
      $news_items[] = $this->createNewsEntity($I);
    }

    $I->logInWithRole('site_manager');
    $node = $this->createNodeWithEntityParagraph($I);
    $this->openParagraphNewsAccordion($I, $node);

    // Add first item.
    $I->fillField('News Items', $news_items[0]->label() . ' (' . $news_items[0]->id() . ')');

    // Add second item.
    $I->click('Add another item', '.field--name-su-entity-news');
    $I->waitForElementVisible('[name="su_entity_news[1][target_id]"]');
    $I->fillField('[name="su_entity_news[1][target_id]"]', $news_items[1]->label() . ' (' . $news_items[1]->id() . ')');

    // Add third item.
    $I->click('Add another item', '.field--name-su-entity-news');
    $I->waitForElementVisible('[name="su_entity_news[2][target_id]"]');
    $I->fillField('[name="su_entity_news[2][target_id]"]', $news_items[2]->label() . ' (' . $news_items[2]->id() . ')');

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');

    $I->canSee('has been updated');
    foreach ($news_items as $news) {
      $I->canSee($news->label());
    }
  }

  /**
   * Test display mode selector with negate: true filtering.
   */
  public function testDisplayModeSelector(FunctionalTester $I) {
    $I->logInWithRole('site_manager');
    $node = $this->createNodeWithEntityParagraph($I);
    $this->openParagraphNewsAccordion($I, $node);

    $I->canSee('News Display');

    // Check that it only contains the expected display modes.
    $select_options = $I->grabMultiple('select[name="su_entity_news_display"] option', 'value');
    $I->assertContains('stanford_card', $select_options);
    $I->assertContains('stanford_h3_card', $select_options);
    $I->assertContains('spotlight_teaser_large_image', $select_options);
    $I->assertContains('spotlight_teaser_small_image', $select_options);

    // Other display modes should be filtered out.
    $I->assertNotContains('default', $select_options);
    $I->assertNotContains('teaser', $select_options);
  }

  /**
   * Test news items work alongside traditional entity reference.
   */
  public function testNewsWithTraditionalEntityReference(FunctionalTester $I) {
    $news = $this->createNewsEntity($I);
    $event = $I->createEntity([
      'type' => 'stanford_event',
      'title' => $this->faker->words(3, TRUE),
    ]);

    $I->logInWithRole('site_manager');
    $node = $this->createNodeWithEntityParagraph($I);
    $this->openParagraphNewsAccordion($I, $node);

    $I->fillField('News Items', $news->label() . ' (' . $news->id() . ')');
    $I->fillField('Content Item(s)', $event->label() . ' (' . $event->id() . ')');

    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');

    $I->canSee('has been updated');
    $I->canSee($news->label());
    $I->canSee($event->label());
  }

  /**
   * Test News Display field is hidden from front-end.
   */
  public function testNewsDisplayFieldHidden(FunctionalTester $I) {
    $news = $this->createNewsEntity($I);
    $I->logInWithRole('site_manager');
    $node = $this->createNodeWithEntityParagraph($I);
    $this->openParagraphNewsAccordion($I, $node);

    $I->fillField('News Items', $news->label() . ' (' . $news->id() . ')');
    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');

    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($news->label());
    $I->dontSee('News Display:');
  }

  /**
   * Test spotlight teaser display modes render correctly.
   */
  #[CodeceptionAttribute\Examples(displayMode: 'Spotlight Teaser - Large Image', variantClass: '.news-spotlight--large')]
  #[CodeceptionAttribute\Examples(displayMode: 'Spotlight Teaser - Small Image', variantClass: '.news-teaser--small')]
  public function testSpotlightTeaserDisplayModes(FunctionalTester $I, Example $example) {
    $news = $this->createNewsEntity($I);
    $I->logInWithRole('site_manager');
    $node = $this->createNodeWithEntityParagraph($I);
    $this->openParagraphNewsAccordion($I, $node);

    $I->fillField('News Items', $news->label() . ' (' . $news->id() . ')');
    $I->selectOption('News Display', $example['displayMode']);
    $I->click('Save', '.ui-dialog-buttonpane');
    $I->waitForElementNotVisible('.ui-dialog');
    $I->click('Save');

    $I->canSee('has been updated');
    $I->canSee($news->label(), '.news-spotlight-teaser');
    $I->seeElement('.news-spotlight-teaser' . $example['variantClass']);
    $I->canSee($news->get('su_news_quote')->value, '.spotlight-quote');
  }

  /**
   * Create a news entity for testing.
   */
  protected function createNewsEntity(FunctionalTester $I) {
    return $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
      'layout_selection' => 'news_spotlight',
      'su_news_quote' => $this->faker->sentence(),
      'su_news_publishing_date' => time(),
    ]);
  }

  /**
   * Open paragraph edit dialog and News accordion.
   */
  protected function openParagraphNewsAccordion(FunctionalTester $I, $node) {
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForElement('.ui-dialog');
    $I->click('[data-drupal-selector="edit-group-news"] summary');
    $I->waitForElementVisible('[name="su_entity_news[0][target_id]"]');
  }

  /**
   * Create a node with stanford_entity paragraph.
   */
  protected function createNodeWithEntityParagraph(FunctionalTester $I) {
    $paragraph = $I->createEntity([
      'type' => 'stanford_entity',
      'su_entity_headline' => $this->faker->words(3, TRUE),
      'su_entity_description' => [
        'format' => 'stanford_html',
        'value' => $this->faker->sentence(),
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
