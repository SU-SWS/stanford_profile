<?php

use Faker\Factory;

/**
 * Class PublicationsCest.
 *
 * @group content
 */
class PublicationsCest {

  /**
   * Create a book citation
   */
  public function testBookCitation(AcceptanceTester $I) {
    $I->createEntity([
      'vid' => 'stanford_publication_topics',
      'name' => 'Foo Bar',
    ], 'taxonomy_term');

    $faker = Factory::create();
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'Test Publication');
    $I->fillField('Publication Topic Terms (value 1)', 'Foo Bar');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Book');
    $I->click('Add Citation');
    $I->fillField('First Name', $faker->firstName);
    $I->fillField('Last Name', $faker->lastName);
    $I->fillField('Subtitle', $faker->text);
    $I->fillField('Publication Place', $faker->text);
    $I->fillField('Publisher', $faker->text);
    $I->fillField('Year', $faker->numberBetween(1900, 2020));
    $I->fillField('su_publication_cta[0][uri]', $faker->url);
    $I->fillField('Link text', $faker->text);

    $I->click('Save');
    $I->canSee('Test Publication', 'h1');
  }

  /**
   * Test out the list pages.
   */
  public function testAllPublicationListPage(AcceptanceTester $I) {
    $faker = Factory::create();
    $this->testBookCitation($I);
    $I->amOnPage('/publications');
    $I->canSee('Test Publication');
    $I->click('Foo Bar');
    $I->assertEquals('/publications/foo-bar', $I->grabFromCurrentUrl());
    $I->canSee('Foo Bar', 'h1');
    $I->canSee('Test Publication');
    $I->canSeeLink('Foo Bar');

    $term = $I->createEntity([
      'vid' => 'stanford_publication_topics',
      'name' => $faker->text(10),
    ], 'taxonomy_term');
    \Drupal::service('cache.render')->deleteAll();
    \Drupal::service('router.builder')->rebuild();
    $I->amOnPage('/publications');
    $I->canSeeLink($term->label());
  }

  /**
   * Published checkbox should be hidden on term edit pages.
   */
  public function testTermPublishing(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $term = $I->createEntity([
      'vid' => 'stanford_publication_topics',
      'name' => 'Foo',
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit')->toString());
    $I->cantSee('Published');
  }

}
