<?php

require_once __DIR__ . '/../TestFilesTrait.php';

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;

/**
 * Class PublicationsCest.
 */
#[CodeceptionAttribute\Group('content')]
class PublicationsCest {

  use TestFilesTrait;

  /**
   * Faker generator.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Keyed array of values to save to review later.
   *
   * @var array
   */
  protected $values = [];

  /**
   * Test constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Create a book citation
   */
  public function testBookCitation(AcceptanceTester $I) {
    $this->values['term_name'] = $this->faker->words(3, TRUE);
    $this->values['node_title'] = $this->faker->words(3, TRUE);
    $term = $I->createEntity([
      'vid' => 'stanford_publication_topics',
      'name' => $this->values['term_name'],
    ], 'taxonomy_term');

    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', $this->values['node_title']);
    $I->selectOption('Publication Types (value 1)', $term->id());
    $I->selectOption('su_publication_citation[actions][bundle]', 'Book');
    $I->click('Add Citation');
    $I->fillField('First Name', $this->faker->firstName());
    $I->fillField('Last Name/Company', $this->faker->lastName());
    $I->fillField('Subtitle', $this->faker->text());
    $I->fillField('Publication Place', $this->faker->text());
    $I->fillField('Publisher', $this->faker->text());
    $I->fillField('Year', $this->faker->numberBetween(1900, 2020));
    $I->fillField('su_publication_cta[0][uri]', $this->faker->url());
    $I->fillField('Link text', $this->faker->text());

    $I->click('Save');
    $I->canSee($this->values['node_title'], 'h1');
  }

  /**
   * Test out the list pages.
   */
  public function testAllPublicationListPage(AcceptanceTester $I) {
    $this->testBookCitation($I);

    $I->amOnPage('/publications');
    $I->canSee($this->values['node_title']);
    $I->click($this->values['term_name']);
    $I->canSee($this->values['term_name'], 'h1');
    $I->canSee($this->values['node_title']);
    $I->canSeeLink($this->values['term_name']);

    $term = $I->createEntity([
      'vid' => 'stanford_publication_topics',
      'name' => $this->faker->text(10),
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->click('Save');

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
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->canSeeCheckboxIsChecked('Published');
  }

  /**
   * An "Other" publication type should be available.
   */
  public function testOtherPublication(AcceptanceTester $I) {
    $this->values['node_title'] = $this->faker->words(3, TRUE);
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', $this->values['node_title']);
    $I->selectOption('su_publication_citation[actions][bundle]', 'Other');
    $I->click('Add Citation');
    $I->fillField('First Name', $this->faker->firstName());
    $I->fillField('Last Name/Company', $this->faker->lastName());
    $I->fillField('Subtitle', $this->faker->text());
    $I->fillField('Publisher', $this->faker->text());
    $I->fillField('su_publication_cta[0][uri]', $this->faker->url());
    $I->fillField('Link text', $this->faker->text());

    $I->click('Save');
    $I->canSee($this->values['node_title'], 'h1');
    $I->canSee('Publication', '.node-stanford-publication-citation-type');
  }

  /**
   * Publication list should be in date order.
   */
  public function testListSort(AcceptanceTester $I) {
    $this->values['a_node_title'] = 'A' . $this->faker->words(3, TRUE);
    $this->values['b_node_title'] = 'B' . $this->faker->words(3, TRUE);
    $this->values['c_node_title'] = 'C' . $this->faker->words(3, TRUE);

    $I->logInWithRole('site_manager');

    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', $this->values['a_node_title']);
    $I->selectOption('su_publication_citation[actions][bundle]', 'Other');
    $I->click('Add Citation');
    $I->fillField('Year', 2020);
    $I->fillField('Month', 6);
    $I->fillField('Day', 1);
    $I->click('Save');
    $I->canSee($this->values['a_node_title'], 'h1');

    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', $this->values['b_node_title']);
    $I->selectOption('su_publication_citation[actions][bundle]', 'Other');
    $I->click('Add Citation');
    $I->fillField('Year', 2020);
    $I->fillField('Month', 6);
    $I->fillField('Day', 15);
    $I->click('Save');
    $I->canSee($this->values['b_node_title'], 'h1');

    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', $this->values['c_node_title']);
    $I->selectOption('su_publication_citation[actions][bundle]', 'Other');
    $I->click('Add Citation');
    $I->fillField('Year', 2020);
    $I->fillField('Month', 1);
    $I->fillField('Day', 15);
    $I->click('Save');
    $I->canSee($this->values['c_node_title'], 'h1');

    $I->amOnPage('/publications');
    $titles = $I->grabMultiple('.csl-entry a');
    foreach ($titles as &$title) {
      $title = preg_replace('/[^\da-z ]/i', '', $title);
    }

    $a_pos = array_search($this->values['a_node_title'], $titles);
    $b_pos = array_search('B June 15th Pub', $titles);
    $c_pos = array_search($this->values['c_node_title'], $titles);

    $I->assertGreaterThan($b_pos, $a_pos, sprintf('"%s" does not display after "%s"', $this->values['a_node_title'], $this->values['b_node_title']));
    $I->assertGreaterThan($a_pos, $c_pos, sprintf('"%s" does not display after "%s"', $this->values['c_node_title'], $this->values['a_node_title']));
    $I->assertGreaterThan($b_pos, $c_pos, sprintf('"%s" does not display after "%s"', $this->values['c_node_title'], $this->values['b_node_title']));

    // Ensure distinct results
    $titles_counts = array_count_values($titles);
    $I->assertEquals(1, $titles_counts[$this->values['a_node_title']]);
    $I->assertEquals(1, $titles_counts[$this->values['b_node_title']]);
    $I->assertEquals(1, $titles_counts[$this->values['c_node_title']]);
  }

  /**
   * Publications should automatically populate on author's page.
   */
  #[CodeceptionAttribute\Group('D8CORE-4867')]
  public function testPubAuthorPage(AcceptanceTester $I) {
    $first_name = $this->faker->firstName();
    $last_name = $this->faker->lastName();
    $author_node = $I->createEntity([
      'type' => 'stanford_person',
      'su_person_first_name' => $first_name,
      'su_person_last_name' => $last_name,
    ]);
    $I->amOnPage($author_node->toUrl()->toString());
    $I->dontSee('Publications', 'h2');
    $publication = $I->createEntity([
      'type' => 'stanford_publication',
      'title' => $this->faker->words(3, TRUE),
      'su_publication_author_ref' => $author_node,
    ]);
    $I->logInWithRole('contributor');
    $I->amOnPage($publication->toUrl('edit-form')->toString());
    $I->selectOption('su_publication_citation[actions][bundle]', 'Book');
    $I->click('Add Citation');
    $I->fillField('First Name', $first_name);
    $I->fillField('Last Name/Company', $last_name);
    $I->fillField('Subtitle', $this->faker->words(2, TRUE));
    $I->fillField('Year', date('Y'));
    $I->fillField('Publisher', $this->faker->company());
    $I->click('Save');

    $I->amOnPage($author_node->toUrl()->toString());
    $I->canSee('Publications', 'h2');
    $I->canSee($publication->label());
  }

  /**
   * Journal citations should include a field for journal publisher.
   */
  public function testJournalPublisher(AcceptanceTester $I) {
    $term = $I->createEntity([
      'vid' => 'stanford_publication_topics',
      'name' => $this->faker->word(),
    ], 'taxonomy_term');

    $this->values['node_title'] = $this->faker->words(3, TRUE);
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_publication');
    $I->fillField('Title', $this->values['node_title']);
    $I->selectOption('Publication Types (value 1)', $term->id());
    $I->selectOption('su_publication_citation[actions][bundle]', 'Journal Article');
    $I->click('Add Citation');
    $I->fillField('First Name', $this->faker->firstName());
    $I->fillField('Last Name/Company', $this->faker->lastName());
    $I->fillField('Volume', "1");
    $I->fillField('Issue', "1");
    $I->fillField('Page(s)', "1-10");
    $I->fillField('Publisher', $this->faker->text());
    $I->fillField('Journal Name', $this->faker->text());
    $I->fillField('Year', $this->faker->numberBetween(1900, 2020));
    $I->click('Save');
    $I->canSee($this->values['node_title'], 'h1');
    $I->canSee('Journal Name');
    $I->canSee('Publisher');
  }

  #[CodeceptionAttribute\Group('publication-importer')]
  public function testPublicationImporter(AcceptanceTester $I) {
    $topic = $I->createEntity([
      'vid' => 'stanford_publication_topics',
      'name' => $this->faker->unique()->words(2, TRUE),
    ], 'taxonomy_term');
    $topic2 = $I->createEntity([
      'vid' => 'stanford_publication_topics',
      'name' => $this->faker->unique()->words(2, TRUE),
    ], 'taxonomy_term');
    $pubTypes = [
      'Article Newspaper/Magazine',
      'Book',
      'Journal Article',
      'Other',
      'Thesis',
    ];
    foreach ($pubTypes as $type) {
      $csv_data[] = [
        'id' => $this->faker->uuid(),
        'citationType' => $type,
        'title' => $type . ': ' . $this->faker->unique()->word(),
        'subtitle' => $this->faker->words(5, TRUE),
        'authors' => $this->faker->firstName() . ' ' . $this->faker->lastName() . '|' . $this->faker->firstName() . ' ' . $this->faker->lastName(),
        'pubPlace' => $this->faker->words(3, TRUE),
        'publisher' => $this->faker->words(3, TRUE),
        'volume' => $this->faker->words(3, TRUE),
        'issue' => $this->faker->numberBetween(1, 100),
        'edition' => $this->faker->numberBetween(1, 100),
        'page' => $this->faker->numberBetween(10, 50),
        'month' => $this->faker->numberBetween(1, 12),
        'day' => $this->faker->numberBetween(1, 25),
        'year' => $this->faker->year(),
        'doi' => $this->faker->numberBetween(1, 50) . '/' . $this->faker->numberBetween(1, 50),
        'url' => $this->faker->url(),
        'image' => '',
        'topics' => $topic->label() . '|' . $topic2->label(),
        'body' => $this->faker->sentences(3, TRUE),
        'ctaUrl' => $this->faker->url(),
        'ctaTitle' => $this->faker->words(3, TRUE),
        'genre' => '',
      ];
    }

    $handle = fopen('php://temp', 'r+');
    fputcsv($handle, array_keys($csv_data[0]));
    foreach ($csv_data as $row) {
      fputcsv($handle, $row);
    }
    rewind($handle);
    $csv_contents = stream_get_contents($handle);
    fclose($handle);

    $csv_file = $this->createFile('pub-import.csv', $csv_contents);

    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/structure/migrate/manage/default/migrations/stanford_publications/csv-upload');
    $I->attachFile('files[csv]', $csv_file);
    $I->click('Save and Import');

    $I->canSee('5 created');

    foreach ($csv_data as $publication) {
      $I->amOnPage('/admin/content');

      $I->canSee($publication['title']);
      $I->click($publication['title']);
      $I->canSee($publication['title'], 'h1');

      $I->click('Edit', '.tabs.primary');
      $I->canSeeInCurrentUrl('/edit');
      $I->click('Edit', '.field--name-su-publication-citation');

      $I->canSeeInField('Title', $publication['title']);
      $topics = explode('|', $publication['topics']);
      $I->canSeeOptionIsSelected('Publication Types (value 1)', $topics[0]);
      $I->canSeeOptionIsSelected('Publication Types (value 2)', $topics[1]);
      $I->canSee($publication['body'], '.su-wysiwyg-text');
      $I->canSeeInField('su_publication_cta[0][uri]', $publication['ctaUrl']);
      $I->canSeeInField('su_publication_cta[0][title]', $publication['ctaTitle']);

      $authors = explode('|', $publication['authors']);
      [$authorFirst, $authorLast] = explode(' ', $authors[0]);
      $I->canSeeInField('First Name', $authorFirst);
      $I->canSeeInField('Last Name/Company', $authorLast);

      [$authorFirst, $authorLast] = explode(' ', $authors[1]);
      $I->canSeeInField('su_publication_citation[form][inline_entity_form][entities][0][form][su_author][1][given]', $authorFirst);
      $I->canSeeInField('su_publication_citation[form][inline_entity_form][entities][0][form][su_author][1][family]', $authorLast);
      $I->canSeeInField('Year', $publication['year']);
      $I->canSeeInField('External Source', $publication['url']);

      if (!in_array($publication['citationType'], [
        'Journal Article',
        'Article Newspaper/Magazine',
        'Thesis',
      ])) {
        $I->canSeeInField('Subtitle', $publication['subtitle']);
      }

      if (!in_array($publication['citationType'], [
        'Book',
        'Journal Article',
        'Article Newspaper/Magazine',
        'Thesis',
        'Other',
      ])) {
        $I->canSeeInField('Journal Name', $publication['issue']);
      }

      if (!in_array($publication['citationType'], [
        'Book',
        'Article Newspaper/Magazine',
        'Thesis',
        'Other',
      ])) {
        $I->canSeeInField('Volume', $publication['volume']);
        $I->canSeeInField('Issue', $publication['issue']);
      }

      if (!in_array($publication['citationType'], ['Book'])) {
        $I->canSeeInField('Month', $publication['month']);
        $I->canSeeInField('Day', $publication['day']);
      }

      if (!in_array($publication['citationType'], [
        'Journal Article',
        'Article Newspaper/Magazine',
        'Thesis',
        'Other',
      ])) {
        $I->canSeeInField('Publication Place', $publication['pubPlace']);
      }
      if (!in_array($publication['citationType'], ['Journal Article'])) {
        $I->canSeeInField('Publisher', $publication['publisher']);
      }

      if (!in_array($publication['citationType'], [
        'Thesis',
        'Other',
        'Article Newspaper/Magazine',
      ])) {
        $I->canSeeInField('Page(s)', $publication['page']);
      }
      if (!in_array($publication['citationType'], [
        'Journal Article',
        'Thesis',
        'Other',
        'Article Newspaper/Magazine',
      ])) {
        $I->canSeeInField('Edition', $publication['edition']);
      }
      if (!in_array($publication['citationType'], [
        'Article Newspaper/Magazine',
        'Other',
      ])) {
        $I->canSeeInField('DOI', $publication['doi']);
      }
    }
  }

}
