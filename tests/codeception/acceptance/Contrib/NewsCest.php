<?php

/**
 * Test the news functionality.
 */
class NewsCest {

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
   * Validate the News View Paragraph type exists
   */
  public function testNewsViewsParagraph(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/paragraphs_type');
    $I->canSee('News Views');
    $I->canSee("news_views");
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

    // @TODO Not working yet.
    // Redirect as anon.
    // $I->amOnPage('/news');
    // $I->click(".su-news-list__item a:first-of-type");
    // $I->seeCurrentUrlEquals('http://google.com/');

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

}
