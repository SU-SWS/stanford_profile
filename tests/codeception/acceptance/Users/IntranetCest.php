<?php

/**
 * Class IntranetCest.
 *
 * @group users
 * @group no-parallel
 * @group intranet
 */
class IntranetCest {

  /**
   * Save the Intranet state before the tests and reset after the tests.
   *
   * @var bool
   */
  protected $intranetWasEnabled = FALSE;

  /**
   * Save the Allow File Uploads state before the tests and reset after the
   * tests.
   *
   * @var bool
   */
  protected $fileUploadsWasEnabled = FALSE;

  /**
   * Save the original state.
   */
  public function _before(AcceptanceTester $I) {
    $this->intranetWasEnabled = (bool) $I->runDrush('sget stanford_intranet');
    $this->fileUploadsWasEnabled = (bool) $I->runDrush('sget stanford_intranet.allow_file_uploads');
  }

  /**
   * Set the state back to how it was before the test.
   */
  public function _after(AcceptanceTester $I) {
    $I->runDrush('sset stanford_intranet ' . (int) $this->intranetWasEnabled);
    $I->runDrush('sset stanford_intranet.allow_file_uploads ' . (int) $this->fileUploadsWasEnabled);
    if (file_exists(codecept_data_dir('/test.txt'))) {
      unlink(codecept_data_dir('/test.txt'));
    }
  }

  /**
   * Simple full site access check.
   */
  public function testIntranet(AcceptanceTester $I) {
    if (!$this->intranetWasEnabled) {
      $I->runDrush('sset stanford_intranet 1');
      $I->runDrush('cache-rebuild');
    }

    $I->stopFollowingRedirects();
    $I->amOnPage('/');
    $I->canSeeResponseCodeIsBetween(301, 403);
    $I->canSeeNumberOfElements('.su-multi-menu__menu a', 0);

    $I->startFollowingRedirects();
    $I->logInWithRole('authenticated');
    $I->amOnPage('/');
    $I->canSeeResponseCodeIsSuccessful();
    $I->canSeeNumberOfElements('.su-multi-menu__menu a', [0, 99]);
  }

  /**
   * Test the access of content.
   */
  public function testAccess(AcceptanceTester $I) {
    // Contributors can't set access restrictions.
    $I->runDrush('sset stanford_intranet 0');
    $I->logInWithRole('contributor');
    $I->amOnPage('/node/add/stanford_page');
    $I->cantSee('Access', '.entity-meta__header');
    $I->runDrush('sset stanford_intranet 1');
    $I->amOnPage('/node/add/stanford_page');
    $I->cantSee('Access', '.entity-meta__header');
    $I->amOnPage('/user/logout');

    // Site managers can set access restrictions.
    $I->logInWithRole('site_manager');
    $I->amOnPage('/node/add/stanford_page');
    $I->canSee('Access', '.entity-meta__header');
    $I->canSee('Site Manager');
    $I->cantSeeCheckboxIsChecked('Contributor');
    $I->cantSeeCheckboxIsChecked('Site Manager');

    // Create a node to test access with.
    $I->fillField('Title', 'Test Private Access');
    $I->checkOption('Stanford Student');
    $I->click('Save');
    $I->canSee('Test Private Access', 'h1');
    $I->canSeeResponseCodeIs(200);
    $page_url = $I->grabFromCurrentUrl();
    $I->amOnPage('/user/logout');

    // Anonymous users will get redirected to the login page.
    $I->amOnPage($page_url);
    $I->canSeeInCurrentUrl('/user/login?destination=' . $page_url);

    // Logged in staff will be denied access.
    $I->logInWithRole('stanford_staff');
    $I->amOnPage($page_url);
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/user/logout');

    // Students should be able to see the content.
    $I->logInWithRole('stanford_student');
    $I->amOnPage($page_url);
    $I->canSeeResponseCodeIs(200);
    $I->canSee('Test Private Access', 'h1');
  }

  /**
   * Content should be indexed and results displayed.
   */
  public function testSearchResults(AcceptanceTester $I) {
    $I->runDrush('sset stanford_intranet 1');
    $I->runDrush('sapi-c');
    $quote = 'Life is like a box of chocolates. You never know what you’re going to get.';
    $text_area = $I->createEntity([
      'type' => 'stanford_wysiwyg',
      'su_wysiwyg_text' => [
        [
          'value' => "<p>$quote</p>",
          'format' => 'stanford_html',
        ],
      ],
    ], 'paragraph');
    $node = $I->createEntity([
      'title' => 'Forest Gump',
      'type' => 'stanford_page',
      'su_page_components' => [
        'target_id' => $text_area->id(),
        'target_revision_id' => $text_area->getRevisionId(),
      ],
    ]);
    $I->runDrush('sapi-i');
    $I->logInWithRole('authenticated');
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee($quote);
    $I->amOnPage('/search?key=chocolate');
    $I->canSeeLink('Forest Gump');
  }

  /**
   * Files can only be added when allow_file_uploads state is enabled.
   */
  public function testMediaAccess(AcceptanceTester $I) {
    $I->runDrush('sset stanford_intranet 1');
    $I->runDrush('sset stanford_intranet.allow_file_uploads 1');

    $I->logInWithRole('site_manager');
    $I->amOnPage('/media/add/file');
    $I->canSeeResponseCodeIs(200);
    $I->amOnPage('/user/logout');

    $I->runDrush('sset stanford_intranet.allow_file_uploads 0');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/media/add/file');
    $I->canSeeResponseCodeIs(403);
  }

}
