<?php

/**
 * Class RolesCest.
 *
 * @group users
 */
class RolesCest {

  /**
   * Default roles should exist.
   */
  public function testRolesExist(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/people/roles');
    $I->canSee('Contributor');
    $I->canSee('Site Editor');
    $I->canSee('Site Manager');
    $I->canSee('Site Builder');
    $I->canSee('Site Developer');
    $I->canSee('Administrator');
  }

  /**
   * Contributor role should have some access.
   *
   * @testme
   */
  public function testContributorRole(AcceptanceTester $I) {
    $I->logInWithRole('contributor');

    $I->amOnPage('/node/add/stanford_page');
    $I->cantSee('Layout');

    $allowed_pages = ['/admin/content'];
    $this->runAccessCheck($I, $allowed_pages);
    $this->runAccessCheck($I, [], 403);

  }

  /**
   * Site editor role should have some access.
   */
  public function testSiteEditorRole(AcceptanceTester $I) {
    $I->logInWithRole('contributor');

    $I->amOnPage('/node/add/stanford_page');
    $I->cantSee('Layout');

    $allowed_pages = ['/admin/content'];
    $this->runAccessCheck($I, $allowed_pages);
    $this->runAccessCheck($I, [], 403);
  }

  /**
   * Site manager should have more access.
   */
  public function testSiteManagerRole(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');

    $I->amOnPage('/node/add/stanford_page');
    $I->canSee('Layout');

    $allowed_pages = ['/admin/content'];
    $this->runAccessCheck($I, $allowed_pages);
    $this->runAccessCheck($I, [], 403);
  }

  /**
   * Site builder will get more access than site manager.
   */
  public function testSiteBuilderRole(AcceptanceTester $I) {
    $I->logInWithRole('site_builder');

    $I->amOnPage('/node/add/stanford_page');
    $I->canSee('Layout');

    $allowed_pages = ['/admin/content'];
    $this->runAccessCheck($I, $allowed_pages);
    $this->runAccessCheck($I, [], 403);
  }

  /**
   * Developers have th emost access.
   */
  public function testSiteDeveloperRole(AcceptanceTester $I) {
    $I->logInWithRole('site_developer');

    $I->amOnPage('/node/add/stanford_page');
    $I->canSee('Layout');

    $allowed_pages = ['/admin/content'];
    $this->runAccessCheck($I, $allowed_pages);
    $this->runAccessCheck($I, [], 403);
  }

  /**
   * Run access checks on an array of paths.
   *
   * @param AcceptanceTester $I
   *   Tester.
   * @param array $pages
   *   Array of paths.
   * @param int $status_code
   *   Expected http response code.
   */
  protected function runAccessCheck(AcceptanceTester $I, $pages = [], $status_code = 200) {
    if ($status_code == 403) {
      $pages[] = $this->getFrontPagePath($I) . '/delete';
    }
    foreach ($pages as $page) {
      $I->amOnPage($page);
      $I->canSeeResponseCodeIs($status_code);
    }
  }

  /**
   * Get the machine path of the home page.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   *
   * @return string
   *   Uri path.
   */
  protected function getFrontPagePath(AcceptanceTester $I) {
    $drush_response = $I->runDrush('config-get system.site page.front --include-overridden --format=json');
    $drush_response = json_decode($drush_response, TRUE);
    return $drush_response['system.site:page.front'];
  }

}
