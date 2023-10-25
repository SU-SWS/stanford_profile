<?php

use Drupal\config_pages\Entity\ConfigPages;
use Faker\Factory;
use Drupal\Core\Cache\Cache;

/**
 * Test the events + importer functionality.
 *
 * @group content
 */
class EventsCest {

  /**
   * Faker.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  /**
   * Test Constructor.
   */
  public function __construct() {
    $this->faker = Factory::create();
  }

  public function _after(AcceptanceTester $I) {
    if ($config_page = ConfigPages::load('stanford_events_importer')) {
      $config_page->delete();
    }
  }

  /**
   * Events list intro block is at the top of the page.
   *
   * @group D8CORE-4858
   */
  public function testListIntro(AcceptanceTester $I) {
    // Start with no events.
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['type' => 'stanford_event']);
    foreach ($nodes as $node) {
      $node->delete();
    }

    $I->logInWithRole('site_manager');
    $I->amOnPage('/events');
    $I->canSeeResponseCodeIs(200);
    $I->canSee('No events at this time');

    $term = $I->createEntity([
      'vid' => 'stanford_event_types',
      'name' => $this->faker->words(2, TRUE),
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl()->toString());
    $I->canSeeResponseCodeIs(200);
    $I->canSee('No events at this time');

    $event = $this->createEventNode($I);
    $event->set('su_event_type', $term->id())->save();
    $I->amOnPage($event->toUrl('edit-form')->toString());
    $I->click('Save');
    $I->canSee($event->label(), 'h1');

    $I->amOnPage('/events');
    $I->canSee($event->label());
    $I->cantSee('No events at this time');

    $I->amOnPage($term->toUrl()->toString());
    $I->canSee($event->label());
    $I->cantSee('No events at this time');

    $message = $this->faker->sentence;
    // Set the cache to avoid any unwanted API issues.
    \Drupal::cache()->set('localist_api:https://events.stanford.edu', [
      'data' => [],
      'expires' => time() + 60,
    ], Cache::PERMANENT);

    $I->amOnPage('/admin/config/importers/events-importer');
    $I->fillField('No Results Message', $message);
    $I->click('Save');
    $I->canSee('Events Importer has been', '.messages-list');

    $I->amOnPage($event->toUrl('delete-form')->toString());
    $I->click('Delete');

    $I->amOnPage($term->toUrl()->toString());
    $I->canSee($term->label(), 'h1');
    $I->cantSee($event->label());
    $I->cantSee('No events at this time');
    $I->canSee($message);
  }

  /**
   * Ensure events are in the sitemap.
   */
  public function testXMLSiteMap(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/search/xmlsitemap/settings/node/stanford_event');
    $I->seeOptionIsSelected('#edit-xmlsitemap-status', 'Included');
    $I->seeOptionIsSelected('#edit-xmlsitemap-priority', '0.5 (normal)');
  }

  /**
   * Test Page Title Conditions.
   */
  public function testPageTitleIgnoreCondition(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    // Todo: make theme name dynamic.
    $I->amOnPage('/admin/structure/block/manage/stanford_basic_pagetitle');
    $values = $I->grabValueFrom('#edit-visibility-request-path-pages');
    if (is_string($values)) {
      $values = explode("\n", $values);
    }
    $I->assertContains('/events*', $values);
  }

  /**
   * Test the event content type exists and has at least a couple of fields.
   */
  public function testContentTypeExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/types/manage/stanford_event/fields');
    $I->canSee('body');
    $I->canSee('su_event_date_time');
    $I->canSee('su_event_contact_info');

    $term = $I->createEntity([
      'name' => $this->faker->firstName,
      'vid' => 'stanford_event_types',
    ], 'taxonomy_term');
    $event_node = $this->createEventNode($I);
    $event_node->set('su_event_type', $term->id())->save();
    $I->amOnPage($term->toUrl()->toString());
    $I->canSee($event_node->label());
    $I->canSee('San Francisco');
    $text = $I->grabTextFrom('.su-event-list-item');
    $text = preg_replace('/[ ]+/', ' ', str_replace("\n", ' ', $text));
    preg_match_all('/San Francisco/', $text, $matches);
    $I->assertCount(1, $matches[0], 'More than 1 occurrence of "San Francisco" found on the page');
    $I->amOnPage($event_node->toUrl()->toString());
    $I->canSee('This is additional contact information.');
  }

  /**
   * Test Access to stuff for contrib role.
   */
  public function testContributorPerms(AcceptanceTester $I) {
    $I->logInWithRole('contributor');

    // Can create a node.
    $I->amOnPage('/node/add/stanford_event');
    $I->canSeeResponseCodeIs(200);

    // Can not delete a node that is not theirs but can edit.
    $node = $this->createEventNode($I);
    $id = $node->id();
    $I->amOnPage("/node/$id/edit");
    $I->dontSeeLink('Delete');
    $new_title = $this->faker->words(3, TRUE);
    $I->fillField('Event Title', $new_title);
    $I->click('Save');
    $I->canSee($new_title, 'h1');

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee('Current revision');

    // Can't adjust taxonomy terms.
    $I->amOnPage('/admin/structure/taxonomy/manage/event_audience/overview');
    $I->dontSeeResponseCodeIs(200);

    $I->amOnPage('/admin/structure/taxonomy/manage/stanford_event_types/overview');
    $I->dontSeeResponseCodeIs(200);

    // Can't adjust menu items.
    $I->amOnPage('/admin/structure/menu/manage/stanford-event-types');
    $I->dontSeeResponseCodeIs(200);

    // Can't adjust the importer form.
    $I->amOnPage('/admin/config/importers/events-importer');
    $I->dontSeeResponseCodeIs(200);
  }

  /**
   * Test thing.
   */
  public function testEditorPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');

    // Can create a node.
    $I->amOnPage('/node/add/stanford_event');
    $I->canSeeResponseCodeIs(200);

    // Can delete a node that is not theirs and can edit.
    $node = $this->createEventNode($I);
    $id = $node->id();

    $I->amOnPage("/node/$id/delete");
    $I->canSeeResponseCodeIs(200);
    $I->canSee('This action cannot be undone');

    $I->amOnPage("/node/$id/edit");
    $new_title = $this->faker->words(3, TRUE);
    $I->fillField('Event Title', $new_title);
    $I->click('Save');

    $I->canSee($new_title, 'h1');

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee('Current revision');

    // Can adjust taxonomy terms.
    $I->amOnPage('/admin/structure/taxonomy/manage/event_audience/overview');
    $I->seeResponseCodeIs(200);

    $I->amOnPage('/admin/structure/taxonomy/manage/stanford_event_types/overview');
    $I->seeResponseCodeIs(200);

    // Can't adjust menu items.
    $I->amOnPage('/admin/structure/menu/manage/stanford-event-types');
    $I->seeResponseCodeIs(200);

    // Can't adjust the importer form.
    $I->amOnPage('/admin/config/importers/events-importer');
    $I->dontSeeResponseCodeIs(200);
  }

  /**
   * Test thing.
   */
  public function testSiteManagerPerms(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    // Can create a node.
    $I->amOnPage('/node/add/stanford_event');
    $I->canSeeResponseCodeIs(200);

    // Can delete a node that is not theirs and can edit.
    $node = $this->createEventNode($I);
    $id = $node->id();

    $I->amOnPage("/node/$id/delete");
    $I->canSeeResponseCodeIs(200);
    $I->canSee('This action cannot be undone');

    $I->amOnPage("/node/$id/edit");
    $new_title = $this->faker->words(3, TRUE);
    $I->fillField('Event Title', $new_title);
    $I->click('Save');
    $I->canSee($new_title);

    // Can see revisions.
    $I->amOnPage("/node/$id/revisions");
    $I->canSee('Current revision');

    // Can adjust taxonomy terms.
    $I->amOnPage('/admin/structure/taxonomy/manage/event_audience/overview');
    $I->seeResponseCodeIs(200);

    $I->amOnPage('/admin/structure/taxonomy/manage/stanford_event_types/overview');
    $I->seeResponseCodeIs(200);

    // Can adjust menu items.
    $I->amOnPage('/admin/structure/menu/manage/stanford-event-types');
    $I->seeResponseCodeIs(200);

    // Can adjust the importer form.
    $I->amOnPage('/admin/config/importers/events-importer');
    $I->seeResponseCodeIs(200);
  }

  /**
   * Test to make sure the main menu link is there.
   */
  public function testDefaultContentExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    // Events Main Menu Link.
    $I->amOnPage('/admin/structure/menu/manage/main');
    $I->canSee('Events');
  }

  /**
   * Published checkbox should be hidden on term edit pages.
   */
  public function testTermPublishing(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $term = $I->createEntity([
      'vid' => 'event_audience',
      'name' => $this->faker->word,
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->cantSee('Published');

    $term = $I->createEntity([
      'vid' => 'stanford_event_types',
      'name' => $this->faker->word,
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->cantSee('Published');
  }

  /**
   * Clone events get incremented date.
   */
  public function testClone(AcceptanceTester $I) {
    $user = $I->createUserWithRoles(['contributor']);
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->createEventNode($I);
    $node->set('uid', $user->id())->save();
    $original_date_time = (int) $node->get('su_event_date_time')[0]->get('value')
      ->getString();
    $I->logInAs($user->getAccountName());
    $I->amOnPage('/admin/content');

    $I->checkOption('tr:contains("' . $node->label() . '") input[name^="views_bulk_operations_bulk_form"]');
    $I->selectOption('Action', 'Clone selected content');
    $I->click('Apply to selected items');
    $I->selectOption('Clone how many times', '2');
    $I->selectOption('Increment Amount', '3');
    $I->selectOption('Units', 'Month');
    $I->click('Apply');

    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $nids = $node_storage->getQuery()
      ->condition('type', 'stanford_event')
      ->sort('nid', 'DESC')
      ->range(0, 1)
      ->accessCheck(FALSE)
      ->execute();
    $cloned_node = $node_storage->load(reset($nids));
    $cloned_date_time = $cloned_node->get('su_event_date_time')[0]->get('value')
      ->getString();

    $I->assertNotEquals($cloned_date_time, $original_date_time);
    $diff = $cloned_date_time - $original_date_time;
    $I->assertEquals(6, round($diff / (60 * 60 * 24 * 30.5)));
  }

  /**
   * Test event card markup.
   *
   * @group eventcard
   */
  public function testEventCard(AcceptanceTester $I) {
    $event = $this->createEventNode($I);
    $view_builder = \Drupal::entityTypeManager()->getViewBuilder('node');
    $pre_render = $view_builder->view($event, 'stanford_card');
    $render_output = \Drupal::service('renderer')->renderPlain($pre_render);

    libxml_use_internal_errors(TRUE);
    $dom = new DOMDocument();
    $dom->loadHTML($render_output);
    $xpath = new DOMXPath($dom);

    $month = $xpath->query('//span[@class="su-event-start-month"]');
    $I->assertNotEmpty($month);
    $I->assertEquals(self::getDateTimeString('M', time()), preg_replace('/(\r\n|\n|\r)/', '', $month->item(0)->nodeValue), 'Start Month does not match');

    $day = $xpath->query('//span[@class="su-event-start-date"]');
    $I->assertNotEmpty($day);
    $I->assertEquals(self::getDateTimeString('j', time()), preg_replace('/(\r\n|\n|\r)/', '', $day->item(0)->nodeValue), 'Start Date does not match');

    $month = $xpath->query('//span[@class="su-event-end-month"]');
    $I->assertNotEmpty($month);
    $I->assertEquals(self::getDateTimeString('M', time() + (60 * 60 * 24)), preg_replace('/(\r\n|\n|\r)/', '', $month->item(0)->nodeValue), 'End Month does not match');

    $day = $xpath->query('//span[@class="su-event-end-date"]');
    $I->assertNotEmpty($day);
    $I->assertEquals(self::getDateTimeString('j', time() + (60 * 60 * 24)), preg_replace('/(\r\n|\n|\r)/', '', $day->item(0)->nodeValue), 'End Date does not match');
  }

  protected static function getDateTimeString($format, $time) {
    $timezone = \Drupal::config('system.date')
      ->get('timezone.default') ?: @date_default_timezone_get();
    return \Drupal::service('date.formatter')
      ->format($time, 'custom', $format, $timezone);
  }

  /**
   * Create an Event Node.
   *
   * @param AcceptanceTester $I
   *   Codeception AcceptanceTester
   * @param bool $external
   *   Wether or not this node should be external.
   * @param string $node_title
   *   A title string to call the new node.
   *
   * @return object
   *   Node Object
   */
  protected function createEventNode(AcceptanceTester $I, $external = FALSE, $node_title = NULL) {
    return $I->createEntity([
      'type' => 'stanford_event',
      'title' => $node_title ?: $this->faker->words(3, TRUE),
      'body' => [
        'value' => '<p>More updates to come.</p>',
        'summary' => '',
      ],
      'su_event_cta' => [
        'uri' => 'https://google.com/',
        'title' => 'This is cta link text',
      ],
      'su_event_email' => 'noreply@stanford.edu',
      'su_event_telephone' => '555-555-5645',
      'su_event_contact_info' => 'This is additional contact information.',
      'su_event_date_time' => [
        'value' => time(),
        'end_value' => time() + (60 * 60 * 24),
        'duration' => (60 * 24),
        'timezone' => 'America/Los_Angeles',
      ],
      'su_event_dek' => 'This is a dek field',
      'su_event_alt_loc' => $external ? 'https://events-legacy.stanford.edu/' : '',
      'su_event_source' => $external ? [
        'uri' => 'http://events-legacy.stanford.edu/events/880/88074',
        'title' => '',
      ] : '',
      'su_event_location' => $external ?: [
        'langcode' => '',
        'country_code' => 'US',
        'administrative_area' => 'CA',
        'locality' => 'San Francisco',
        'postal_code' => '94123-2806',
        'address_line1' => '1901 Lombard St',
        'address_line2' => '',
        'organization' => 'Asfdasdfa sdfasd fasf',
      ],
      'su_event_map_link' => [
        'uri' => 'https://stanford.edu/',
        'title' => 'map link',
      ],
      'su_event_sponsor' => [
        'This is a sponsor',
        'This is two sponsor',
        'This is 3 sponsor',
      ],
      'su_event_subheadline' => 'This is a sub-headline',
    ]);
  }

}
