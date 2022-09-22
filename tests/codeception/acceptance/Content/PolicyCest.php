<?php

use Faker\Factory;

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

  /**
   * Test field access.
   */
  public function testPolicyAccess(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_policy');
    $I->cantSee('Create a new book');
    // This indicates they can add to an existing book.
    $I->canSeeOptionIsSelected('Book', '- None -');

    $I->amOnPage('/user/logout');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_policy');
    $I->canSee('Create a new book');
  }

  /**
   * Test the path auto settings.
   */
  public function testPolicyPathAuto(AcceptanceTester $I) {
    $title = $this->faker->words(4, TRUE) . ' foo bar';
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_policy');
    $I->fillField('Policy Title', $title);
    $I->uncheckOption('Automatic Prefix');
    $I->fillField('Chapter Number', 1);
    $I->fillField('SubChapter Number', 2);
    $I->fillField('Policy Number', 3);
    $I->click('Save');
    $I->canSee("1.2.3 $title", 'h1');
    $current_url = $I->grabFromCurrentUrl();
    $I->assertStringContainsString('-foo-bar', $current_url);

    $parent_page = $I->createEntity([
      'type' => 'stanford_page',
      'title' => $this->faker->words(2, TRUE) . '-bar-baz',
    ]);
    $I->amOnPage($parent_page->toUrl('edit-form')->toString());
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', $parent_page->label());
    $I->click('Save');
    $I->canSeeLink($parent_page->label());

    $book = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(2, TRUE) . '-baz-foo',
    ]);
    $I->amOnPage($book->toUrl('edit-form')->toString());
    $I->selectOption('Book', '- Create a new book -');
    $I->click('Change book (update list of parents)');
    $I->click('Save');
    $I->canSee($book->label(), 'h1');

    $node = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(4, TRUE) . '-foo-bar',
    ]);

    $I->amOnPage($node->toUrl('edit-form')->toString());
    $I->checkOption('Provide a menu link');
    $I->fillField('Menu link title', $node->label());
    $I->selectOption('Parent link', '-- ' . $parent_page->label());
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
   * Test the heirarchy of the book.
   */
  public function testPolicyHeirarcy(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $book = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(2, TRUE),
    ]);
    $I->amOnPage($book->toUrl('edit-form')->toString());
    $I->selectOption('Book', '- Create a new book -');
    $I->click('Change book (update list of parents)');
    $I->click('Save');
    $I->canSee($book->label(), 'h1');

    $chapter_one = $I->createEntity([
      'type' => 'stanford_policy',
      'su_policy_title' => $this->faker->words(2, TRUE),
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
    ]);
    $I->amOnPage($article_one->toUrl('edit-form')->toString());
    $I->fillField('su_policy_effective[0][value][date]', date('Y-m-d', time() - 60 * 60 * 24 * 15));
    $I->fillField('su_policy_updated[0][value][date]', date('Y-m-d'));
    $I->fillField('Authority', $authority);
    $I->selectOption('Book', $book->label());
    $I->click('Change book (update list of parents)');
    $I->selectOption('Parent item', '-- 2. ' . $chapter_two->label());
    $I->click('Change book (update list of parents)');

    $I->click('Add new change log');
    $I->canSeeInField('[name="su_policy_changelog[form][0][title][0][value]"]', date('Y-m-d'));
    $I->canSeeInField('[name="su_policy_changelog[form][0][su_policy_date][0][value][date]"]', date('Y-m-d'));
    $change_notes = $this->faker->sentences(3, TRUE);
    $I->fillField('Notes', $change_notes);

    $I->click('Save');

    $I->canSee($article_one->label(), 'h1');
    $I->canSee('Home', '.breadcrumb');
    $I->canSee($book->label(), '.breadcrumb');
    $I->canSee($chapter_two->label(), '.breadcrumb');
    $I->canSee($article_one->label(), '.breadcrumb');

    $I->canSee(date('F d, Y', time() - 60 * 60 * 24 * 15));
    $I->canSee(date('F d, Y'));
    $I->canSee($authority);

    $I->cantSee($change_notes);
  }

}
