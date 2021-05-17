<?php

/**
 * Test the shared tags vocabulary.
 */
class SharedTagsCest {

  /**
   * Validate the Shared Tag vocabulary exists
   */
  public function testSharedVocabExists(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/admin/structure/taxonomy/manage/shared_tags/overview');
    $I->seeResponseCodeIs(200);
  }

  /**
   * Validate the field exists on basic pages, and validate tag creation
   * on a basic page.
   */
  public function testSharedVocabBasicPage(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/node/add/stanford_page');
    $I->canSee('Shared Tags');
    $I->fillField('Title', 'Basic Page Test Title');
    $I->fillField('#edit-su-shared-tags-0-target-id', 'basic page test tag');
    $I->click('Save');
    $I->amOnPage('/basic-page-test-title');
    $I->click('Edit');
    $I->canSee('basic page test tag');
    $I->amOnPage('/admin/structure/taxonomy/manage/shared_tags/overview');
    $I->canSee('basic test tag');
  }

  /**
   * Validate the field exists on Publication, and validate tag creation
   * on a structured content type.
   */
  public function testSharedVocabPublication(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/node/add/stanford_publication');
    $I->canSee('Shared Tags');
    $I->fillField('Title', 'Publication Test Title');
    $I->fillField('#edit-su-shared-tags-0-target-id', 'publication test tag');
    $I->click('Save');
    $I->amOnPage('/publications/publication-test-title');
    $I->click('Edit');
    $I->canSee('publication test tag');
    $I->amOnPage('/admin/structure/taxonomy/manage/shared_tags/overview');
    $I->canSee('publication test tag');
  }

  /**
   * Validate the field exists on Events.
   */
  public function testSharedVocabEvent(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/node/add/stanford_event');
    $I->canSee('Shared Tags');
  }

  /**
   * Validate the field exists on Event Series.
   */
  public function testSharedVocabEventSeries(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/node/add/stanford_event_series');
    $I->canSee('Shared Tags');
  }

  /**
   * Validate the field exists on News
   */
  public function testSharedVocabNews(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/node/add/stanford_news');
    $I->canSee('Shared Tags');
  }

  /**
   * Validate the field exists on Person.
   */
  public function testSharedVocabPerson(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');
    $I->amOnPage('/node/add/stanford_person');
    $I->canSee('Shared Tags');
  }

}
