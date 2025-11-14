<?php

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;

/**
 * Card paragraph tests.
 */
#[CodeceptionAttribute\Group('paragraphs')]
#[CodeceptionAttribute\Group('lists')]
class ListCest {

  /**
   * Faker service.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  #[CodeceptionAttribute\Group('argument-suggestion')]
  public function testArgumentSuggestion(FunctionalTester $I) {
    $news_term = $I->createEntity([
      'vid' => 'stanford_news_topics',
      'name' => $this->faker->unique()->uuid(),
    ], 'taxonomy_term');

    $event_term = $I->createEntity([
      'vid' => 'stanford_event_types',
      'name' => $this->faker->unique()->uuid(),
    ], 'taxonomy_term');

    $person_term = $I->createEntity([
      'vid' => 'stanford_person_types',
      'name' => $this->faker->unique()->uuid(),
    ], 'taxonomy_term');

    $paragraph = $I->createEntity([
      'type' => 'stanford_lists',
      'su_list_headline' => $this->faker->words(3, TRUE),
    ], 'paragraph');

    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->text(30),
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
      ],
    ]);

    $I->logInWithRole('site_manager');
    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->scrollTo('.js-lpb-component', 0, -100);
    $I->moveMouseOver('.js-lpb-component', 10, 10);
    $I->click('Edit', '.lpb-controls');
    $I->waitForText('List Format');
    $I->canSee('Advanced Options');

    $I->selectOption('View', 'News');
    $I->waitForAjaxToFinish();
    $I->selectOption('Display', 'News Card Grid');
    $I->click('.viewfield-autocomplete summary');
    $I->fillField('Arguments', substr($person_term->label(), 0, 3));
    $I->wait(1);
    $I->cantSee($person_term->label());
    $I->fillField('Arguments', substr($news_term->label(), 0, 3));

    $I->waitForText($news_term->label());
    $I->click($news_term->label());
    $I->canSeeInField('Arguments', $news_term->label());
    $I->clearField('Arguments');

    $I->selectOption('View', 'Events');
    $I->waitForAjaxToFinish();

    $I->fillField('Arguments', substr($person_term->label(), 0, 3));
    $I->wait(1);
    $I->cantSee($person_term->label());
    $I->fillField('Arguments', substr($event_term->label(), 0, 3));

    $I->waitForText($event_term->label());
    $I->click($event_term->label());
    $I->canSeeInField('Arguments', $event_term->label());
    $I->clearField('Arguments');

    $I->selectOption('View', 'People');
    $I->waitForAjaxToFinish();

    $I->fillField('Arguments', substr($event_term->label(), 0, 3));
    $I->wait(1);
    $I->cantSee($event_term->label());
    $I->fillField('Arguments', substr($person_term->label(), 0, 3));

    $I->waitForText($person_term->label());
    $I->click($person_term->label());
    $I->canSeeInField('Arguments', $person_term->label());
  }

}
