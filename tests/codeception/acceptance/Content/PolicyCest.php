<?php

use Faker\Factory;
use Drupal\config_pages\Entity\ConfigPages;

/**
 * Test policy content type.
 *
 * @group content
 * @group policy
 */
class PolicyCest {

  /**
   * Faker provider.
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

  public function _before(AcceptanceTester $I) {
    if ($config_page = ConfigPages::load('policy_settings')) {
      $config_page->delete();
    }
  }

  public function _after(AcceptanceTester $I) {
    $this->_before($I);
  }

  /**
   * Test field access.
   */
  public function testPolicyAccess(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_policy');
    $I->cantSee('Create a new book');
    // D8CORE-4551 - removed create policy permission for contributors
    $I->canSee('Access Denied');
    $book = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(2, TRUE) . '-baz-foo',
      'su_policy_auto_prefix' => 1,
    ]);
    $I->amOnPage($book->toUrl('edit-form')->toString());
    // This indicates they can add to an existing book.
    $I->canSeeOptionIsSelected('Book', '- None -');
    $I->cantSee('Policy Prefix');

    $I->amOnPage('/user/logout');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_policy');
    $I->canSee('Create a new book');
    $I->cantSee('Policy Prefix');

    $I->amOnPage('/user/logout');
    $I->logInWithRole('administrator');
    $I->amOnPage('/node/add/stanford_policy');
    $I->canSee('Create a new book');
    $I->canSee('Policy Prefix');
    $I->canSee('Chapter Number');
  }

  /**
   * Test book title changes.
   */
  public function testPolicyTitle(AcceptanceTester $I) {
    $title = $this->faker->words(4, TRUE) . ' foo bar';
    $I->logInWithRole('administrator');
    $I->amOnPage('/node/add/stanford_policy');
    $I->fillField('Policy Title', $title);
    $I->click('Save');
    $I->canSee($title, 'h1');

    $I->click('Edit', '.tabs');
    $new_title = $this->faker->words(4, TRUE) . ' bar foo';
    $I->fillField('Policy Title', $new_title);
    $I->click('Save');
    $I->canSee($new_title, 'h1');
    $I->cantSee($title);
  }

  /**
   * Test the path auto settings.
   *
   * @group menu_link_weight
   */
  public function testPolicyPathAuto(AcceptanceTester $I) {
    $title = $this->faker->words(4, TRUE) . ' foo bar';
    $I->logInWithRole('administrator');
    $I->amOnPage('/node/add/stanford_policy');
    $I->fillField('Policy Title', $title);
    $I->uncheckOption('Automatic Prefix');
    $I->fillField('Chapter Number', 1);
    $I->fillField('SubChapter Number', 2);
    $I->fillField('Policy Number', 3);
    $I->click('Save');
    $I->cantSee("1.2.3 $title", 'h1');

    $current_url = $I->grabFromCurrentUrl();
    $I->assertStringContainsString('-foo-bar', $current_url);

    $parent_page = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(2, TRUE) . '-bar-baz',
      'su_policy_auto_prefix' => 1,
    ]);
    $I->amOnPage($parent_page->toUrl('edit-form')->toString());
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', $parent_page->label());
    $I->click('Save');
    $I->canSeeLink($parent_page->label());

    $book = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(2, TRUE) . '-baz-foo',
      'su_policy_auto_prefix' => 1,
    ]);
    $I->amOnPage($book->toUrl('edit-form')->toString());
    $I->selectOption('Book', '- Create a new book -');
    $I->click('Change book (update list of parents)');
    $I->click('Save');
    $I->canSee($book->label(), 'h1');

    $node = $I->createEntity([
      'type' => 'stanford_policy',
      'title' => $this->faker->words(3, TRUE),
      'su_policy_title' => $this->faker->words(4, TRUE) . '-foo-bar',
      'su_policy_auto_prefix' => 1,
    ]);

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', $node->label());
    $I->selectOption('Parent item', 'main:menu_link_field:node_field_menulink_' . $parent_page->uuid() . '_und');
    $I->click('Change parent (update list of weights)');
    $I->click('Save');

    $current_url = $I->grabFromCurrentUrl();
    $I->assertStringContainsString('-bar-baz', $current_url);
    $I->assertStringContainsString('-foo-bar', $current_url);

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->selectOption('Book', $book->label());
    $I->click('Change book (update list of parents)');
    $I->click('Save');
    $I->canSee($node->label(), 'h1');

    $current_url = $I->grabFromCurrentUrl();
    $I->assertStringContainsString('-baz-foo', $current_url);
    $I->assertStringContainsString('-foo-bar', $current_url);
  }

  /**
   * Test the hierarchy of the book.
   *
   * @group menu_link_weight
   */
  public function testPolicyHeirarcy(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $book = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(2, TRUE),
      'su_policy_auto_prefix' => 1,
    ]);
    $I->amOnPage($book->toUrl('edit-form')->toString());
    $I->selectOption('Book', '- Create a new book -');
    $I->click('Change book (update list of parents)');
    $I->click('Save');
    $I->canSee($book->label(), 'h1');

    $chapter_one = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(2, TRUE),
      'su_policy_auto_prefix' => 1,
    ]);
    $I->amOnPage($chapter_one->toUrl('edit-form')->toString());
    $I->selectOption('Book', $book->label());
    $I->click('Change book (update list of parents)');
    $I->click('Save');
    $I->canSee($chapter_one->label(), 'h1');
    $I->canSee('Home', '.breadcrumb');
    $I->canSee($book->label(), '.breadcrumb');
    $I->canSee($chapter_one->label(), '.breadcrumb');

    $chapter_two = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(2, TRUE),
      'su_policy_auto_prefix' => 1,
    ]);
    $I->amOnPage($chapter_two->toUrl('edit-form')->toString());
    $I->selectOption('Book', $book->label());
    $I->click('Change book (update list of parents)');
    $I->click('Save');
    $I->canSee($chapter_two->label(), 'h1');
    $I->canSee('Home', '.breadcrumb');
    $I->canSee($book->label(), '.breadcrumb');
    $I->canSee($chapter_two->label(), '.breadcrumb');

    $authority = substr($this->faker->sentence, 0, 255);

    $article_one = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(2, TRUE),
      'su_policy_auto_prefix' => 1,
    ]);
    $time = \Drupal::time()->getCurrentTime();
    /** @var \Drupal\Core\Datetime\DateFormatterInterface $data_formatter */
    $data_formatter = \Drupal::service('date.formatter');
    $fifteen_days_ago = $time - 60 * 60 * 24 * 15;

    $I->amOnPage($article_one->toUrl('edit-form')->toString());
    $I->fillField('su_policy_effective[0][value][date]', $data_formatter->format($fifteen_days_ago, 'custom', 'Y-m-d', self::getTimezone()));
    $I->fillField('su_policy_updated[0][value][date]', $data_formatter->format($time, 'custom', 'Y-m-d', self::getTimezone()));
    $I->fillField('Authority', $authority);
    $I->selectOption('Book', $book->label());
    $I->click('Change book (update list of parents)');
    $I->selectOption('book[pid]', '-- 2. ' . $chapter_two->label());
    $I->click('Change book (update list of parents)');

    $I->click('Add new policy log');
    $I->fillField('[name="su_policy_changelog[form][0][su_policy_title][0][value]"]', $data_formatter->format($time, 'custom', 'Y-m-d', self::getTimezone()));
    $I->canSeeInField('[name="su_policy_changelog[form][0][su_policy_date][0][value][date]"]', $data_formatter->format($time, 'custom', 'Y-m-d', self::getTimezone()));
    $change_notes = $this->faker->sentences(3, TRUE);
    $I->fillField('Notes', $change_notes);

    $I->click('Save');

    $I->canSee($article_one->label(), 'h1');
    $I->canSee('Home', '.breadcrumb');
    $I->canSee($book->label(), '.breadcrumb');
    $I->canSee($chapter_two->label(), '.breadcrumb');
    $I->canSee($article_one->label(), '.breadcrumb');

    $I->canSee($data_formatter->format($fifteen_days_ago, 'custom', 'F d, Y', self::getTimezone()));
    $I->canSee($data_formatter->format($time, 'custom', 'F d, Y', self::getTimezone()));
    $I->canSee($authority);

    $I->cantSee($change_notes);

    $I->amOnPage($article_one->toUrl()->toString());
    $I->canSee('2.1 ' . $article_one->get('su_policy_title')->getString());

    $I->amOnPage('/admin/config/content/policy');
    $I->selectOption('Prefix First Level', 'Uppercase Roman Numerals');
    $I->selectOption('Prefix Second Level', 'Uppercase Alphabetic');
    $I->click('Save');

    $I->amOnPage($article_one->toUrl()->toString());
    $I->canSee('II.A ' . $article_one->get('su_policy_title')->getString());

    $new_prefix = $this->faker->randomLetter;
    $I->amOnPage($chapter_two->toUrl('edit-form')->toString());
    $I->uncheckOption('Automatic Prefix');
    $I->fillField('Chapter Number', $new_prefix);
    $I->click('Save');
    $I->canSee($new_prefix . '. ' . $chapter_two->get('su_policy_title')
        ->getString(), 'h1');

    $I->amOnPage($article_one->toUrl()->toString());
    $I->canSee($new_prefix . '.A ' . $article_one->get('su_policy_title')
        ->getString());

    $new_title = $this->faker->words(4, TRUE);
    $I->amOnPage($article_one->toUrl('edit-form')->toString());
    $I->fillField('Policy Title', $new_title);
    $I->click('Save');
    $I->canSee($new_title);
  }

  protected static function getTimezone() {
    return \Drupal::config('system.date')
      ->get('timezone.default') ?: @date_default_timezone_get();
  }

}
