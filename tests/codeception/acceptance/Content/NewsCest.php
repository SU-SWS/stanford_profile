<?php

use Faker\Factory;

/**
 * Test the news functionality.
 */
class NewsCest {

  /**
   * News list intro block is at the top of the page.
   */
  public function testListIntro(AcceptanceTester $I) {
    $intro_text = Factory::create()->text();
    $I->logInWithRole('site_manager');
    $I->amOnPage('/news');
    $I->click('Edit Block Content Above');
    $I->click('Add Text Area');
    $I->fillField('Body', $intro_text);
    $I->click('Save');
    $I->canSee($intro_text);
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
    $I->canSeeNumberOfElements(".term-id", 6);
  }

  /**
   * Test that the view pages exist.
   */
  public function testViewPagesExist(AcceptanceTester $I) {
    $I->amOnPage("/news");
    $I->see("No results found");
    $I->seeLink('Faculty');
    $I->click("a[href='/news/staff']");
    $I->canSeeResponseCodeIs(200);
    $I->see("No results found");
    $I->see("Topics Menu");
  }

  /**
   * Validate external content redirect.
   */
  public function testExternalSourceArticle(AcceptanceTester $I) {

    $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Google',
      'su_news_source' => "http://google.com/",
    ]);

    // Redirect as anon.
    $I->runDrush('cr');
    $I->amOnPage('/news');
    $I->click(".su-news-article a:first-of-type");
    $I->seeCurrentUrlEquals('/');

    // See content as admin.
    $I->logInWithRole('administrator');
    $I->amOnPage('/news/google');
    $I->canSeeInCurrentUrl("/news/google");
  }

  /**
   * Test that only two of three new news nodes show up in the more news view
   * on the node page.
   */
  public function testMoreNewsView(AcceptanceTester $I) {
    $I->logInWithRole('administrator');

    $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Test News 1',
    ]);
    $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Test News 2',
    ]);
    $I->createEntity([
      'type' => 'stanford_news',
      'title' => 'Test News 3',
    ]);

    $I->amOnPage("/news/test-news-2");
    $I->canSeeNumberOfElements(".stanford-news--cards .su-card", 2);
    $I->see("Test News 1");
    $I->see("Test News 3");
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
      'name' => 'Foo',
    ], 'taxonomy_term');
    $I->amOnPage($term->toUrl('edit-form')->toString());
    $I->cantSee('Published');
  }

}
