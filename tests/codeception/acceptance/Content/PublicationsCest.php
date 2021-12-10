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
    $I->fillField('Last Name/Company', $faker->lastName);
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
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->cantSee('Published');
  }

  /**
   * An "Other" publication type should be available.
   */
  public function testOtherPublication(AcceptanceTester $I) {
    $faker = Factory::create();
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'Test Publication');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Other');
    $I->click('Add Citation');
    $I->fillField('First Name', $faker->firstName);
    $I->fillField('Last Name/Company', $faker->lastName);
    $I->fillField('Subtitle', $faker->text);
    $I->fillField('Publisher', $faker->text);
    $I->fillField('su_publication_cta[0][uri]', $faker->url);
    $I->fillField('Link text', $faker->text);

    $I->click('Save');
    $I->canSee('Test Publication', 'h1');
    $I->canSee('Publication', '.node-stanford-publication-citation-type');
  }

  /**
   * Publication list should be in date order.
   */
  public function testListSort(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'A June 1st Pub');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Other');
    $I->click('Add Citation');
    $I->fillField('Year', 2020);
    $I->fillField('Month', 6);
    $I->fillField('Day', 1);
    $I->click('Save');
    $I->canSee('A June 1st Pub', 'h1');


    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'B June 15th Pub');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Other');
    $I->click('Add Citation');
    $I->fillField('Year', 2020);
    $I->fillField('Month', 6);
    $I->fillField('Day', 15);
    $I->click('Save');
    $I->canSee('B June 15th Pub', 'h1');

    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', 'C January Pub');
    $I->selectOption('su_publication_citation[actions][bundle]', 'Other');
    $I->click('Add Citation');
    $I->fillField('Year', 2020);
    $I->fillField('Month', 1);
    $I->fillField('Day', 15);
    $I->click('Save');
    $I->canSee('C January Pub', 'h1');

    $I->amOnPage('/publications');
    $titles = $I->grabMultiple('.csl-entry a');
    foreach($titles as &$title){
      $title = preg_replace('/[^\da-z ]/i', '', $title);
    }

    $a_pos = array_search('A June 1st Pub', $titles);
    $b_pos = array_search('B June 15th Pub', $titles);
    $c_pos = array_search('C January Pub', $titles);

    $I->assertGreaterThan($b_pos, $a_pos, '"A June 1st Pub" does not display after "B June 15th Pub"');
    $I->assertGreaterThan($a_pos, $c_pos, '"C January Pub" does not display after "A June 1st Pub"');
    $I->assertGreaterThan($b_pos, $c_pos, '"C January Pub" does not display after "B June 15th Pub"');

    // Ensure distinct results
    $titles_counts = array_count_values($titles);
    $I->assertEquals(1, $titles_counts['A June 1st Pub']);
    $I->assertEquals(1, $titles_counts['B June 15th Pub']);
    $I->assertEquals(1, $titles_counts['C January Pub']);
  }

}
