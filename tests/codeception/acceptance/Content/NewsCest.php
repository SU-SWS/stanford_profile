<?php

use Faker\Factory;

/**
 * Test the news functionality.
 */
class NewsCest {

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

  /**
   * News list intro block is at the top of the page.
   */
  public function testListIntro(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/news');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Test that the default content has installed and is unpublished.
   */
  public function testDefaultContentExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/admin/content");
    $I->see("Sample: Smith Conference");
    $I->see("Sample: For Runners, Is 15 Feet the New 6 Feet for Social Distancing?");
    $I->see("Sample: Stanford researchers find that misfiring from jittery neurons");

    $I->amOnPage("/news/sample-smith-conference");
    $I->see("This page is currently unpublished and not visible to the public.");

    $I->amOnPage("/news/sample-runners-15-feet-new-6-feet-social-distancing");
    $I->see("This page is currently unpublished and not visible to the public.");

    $I->amOnPage("/news/sample-stanford-researchers-find-misfiring-jittery-neurons");
    $I->see("This page is currently unpublished and not visible to the public.");

    $I->see("News", ".su-multi-menu");
  }

  /**
   * Test that the vocabulary and terms exist.
   */
  public function testVocabularyTermsExists(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/admin/structure/taxonomy/manage/stanford_news_topics/overview");
    $I->canSeeNumberOfElements("input.term-id", [2, 99]);
  }

  /**
   * Test that the view pages exist.
   */
  public function testViewPagesExist(AcceptanceTester $I) {
    $I->amOnPage("/news");
    $I->seeLink('Announcement');
    $I->click("a[href='/news/announcement']");
    $I->canSeeResponseCodeIs(200);
    $I->see("News Topics");
  }

  /**
   * Validate external content redirect.
   */
  public function testExternalSourceArticle(AcceptanceTester $I) {

    $node = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, TRUE),
      'su_news_source' => "http://google.com/",
    ]);

    // Redirect as anon.
    $I->amOnPage($node->toUrl()->toString());
    $I->seeCurrentUrlEquals('/');

    // See content as admin.
    $I->logInWithRole('administrator');
    $I->amOnPage($node->toUrl()->toString());
    $I->canSeeInCurrentUrl($node->toUrl()->toString());
  }

  /**
   * Test that only two of three new news nodes show up in the more news view
   * on the node page.
   */
  public function testMoreNewsView(AcceptanceTester $I) {
    $I->logInWithRole('administrator');

   $first_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' => $this->faker->words(3, true),
    ]);
    $second_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' =>  $this->faker->words(3, true),
    ]);
   $third_news = $I->createEntity([
      'type' => 'stanford_news',
      'title' =>  $this->faker->words(3, true),
    ]);

    $I->amOnPage($second_news->toUrl()->toString());
    $I->canSeeNumberOfElements(".stanford-news--cards .su-card", [2, 3]);
  }

  /**
   * Test that the XML sitemap and metatag configuration is set.
   */
  public function testXMLMetaDataRevisions(AcceptanceTester $I) {
    $I->logInWithRole('administrator');

    // Revision Delete is enabled.
    $I->amOnPage('/admin/structure/types/manage/stanford_news');
    $I->seeCheckboxIsChecked("#edit-node-revision-delete-track");
    $I->seeCheckboxIsChecked("#edit-options-revision");
    $I->seeInField("#edit-minimum-revisions-to-keep", 5);

    // XML Sitemap.
    $I->amOnPage("/admin/config/search/xmlsitemap/settings");
    $I->see("News");
    $I->amOnPage("/admin/config/search/xmlsitemap/settings/node/stanford_news");
    $I->selectOption("#edit-xmlsitemap-status", 1);

    // Metatags.
    $I->amOnPage("/admin/config/search/metatag/page_variant__stanford_news_list-layout_builder-0");
    $I->canSeeResponseCodeIs(200);
    $I->amOnPage("/admin/config/search/metatag/page_variant__stanford_news_list_terms-layout_builder-0");
    $I->canSeeResponseCodeIs(200);
    $I->amOnPage("/admin/config/search/metatag/node__stanford_news");
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Published checkbox should be hidden on term edit pages.
   */
  public function testTermPublishing(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $term = $I->createEntity([
      'vid' => 'stanford_news_topics',
      'name' => $this->faker->word,
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->cantSee('Published');
  }

}
