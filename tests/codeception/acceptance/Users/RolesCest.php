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
    $I->amOnPage('/admin/users/roles');
    $I->canSee('Contributor');
    $I->canSee('Site Editor');
    $I->canSee('Site Manager');
    $I->canSee('Site Builder');
    $I->canSee('Site Developer');
    $I->canSee('Administrator');
    $I->canSee('Site Embedder');
  }

  /**
   * Stanford Staff role should be very limited.
   */
  public function testStaffRole(AcceptanceTester $I){
    $I->logInWithRole('stanford_staff');
    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->cantSeeElement('#toolbar-administration');
  }

  /**
   * Stanford Staff role should be very limited.
   */
  public function testStudentRole(AcceptanceTester $I){
    $I->logInWithRole('stanford_student');
    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->cantSeeElement('#toolbar-administration');
  }

  /**
   * Stanford Staff role should be very limited.
   */
  public function testFacultyRole(AcceptanceTester $I){
    $I->logInWithRole('stanford_faculty');
    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->cantSeeElement('#toolbar-administration');
  }

  /**
   * Contributor role should have some access.
   */
  public function testContributorRole(AcceptanceTester $I) {
    $I->logInWithRole('contributor');

    $I->amOnPage('/node/add/stanford_page');
    $I->cantSeeLink('Layout');

    $allowed_pages = ['/admin/content'];
    $this->runAccessCheck($I, $allowed_pages);
    $not_allowed = [$this->getFrontPagePath($I) . '/delete'];
    $this->runAccessCheck($I, $not_allowed, 403);

    $I->amOnPage('/');
    $links = [
      '/admin/content' => 'All Content',
      '/admin/content/media' => 'All Media',
    ];
    $this->runLinkExistCheck($I, $links);

    $links = [
      'Local Footer',
      'Site Settings',
    ];
    $this->runLinkExistCheck($I, $links, FALSE);

    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->canSeeElement('#toolbar-administration');
  }

  /**
   * Site editor role should have some access.
   */
  public function testSiteEditorRole(AcceptanceTester $I) {
    $I->logInWithRole('site_editor');

    $I->amOnPage('/node/add/stanford_page');
    $I->cantSeeLink('Layout');

    $allowed_pages = ['/admin/content'];
    $this->runAccessCheck($I, $allowed_pages);
    $not_allowed = [$this->getFrontPagePath($I) . '/delete'];
    $this->runAccessCheck($I, $not_allowed, 403);

    $I->amOnPage('/');
    $links = [
      '/admin/content' => 'All Content',
      '/admin/content/media' => 'All Media',
    ];
    $this->runLinkExistCheck($I, $links);

    $links = [
      'Local Footer',
      'Site Settings',
    ];
    $this->runLinkExistCheck($I, $links, FALSE);

    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->canSeeElement('#toolbar-administration');
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
    $not_allowed = [$this->getFrontPagePath($I) . '/delete'];
    $this->runAccessCheck($I, $not_allowed, 403);

    $I->amOnPage('/');
    $links = [
      '/admin/content' => 'All Content',
      '/admin/content/media' => 'All Media',
      '/admin/config/system/local-footer' => 'Local Footer',
      '/admin/config/system/basic-site-settings' => 'Site Settings',
    ];
    $this->runLinkExistCheck($I, $links);

    $links = [
      '/admin/appearance/settings' => 'Settings',
    ];
    $this->runLinkExistCheck($I, $links, FALSE);

    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->canSeeElement('#toolbar-administration');
  }

  /**
   * Site builder will get more access than site manager.
   */
  public function testSiteBuilderRole(AcceptanceTester $I) {
    $I->logInWithRole('site_builder');

    $I->amOnPage('/node/add/stanford_page');
    $I->canSee('Layout');

    $allowed_pages = [
      '/admin/content',
      $this->getFrontPagePath($I) . '/delete',
    ];
    $this->runAccessCheck($I, $allowed_pages);

    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->canSeeElement('#toolbar-administration');
  }

  /**
   * Developers have the most access.
   */
  public function testSiteDeveloperRole(AcceptanceTester $I) {
    $I->logInWithRole('site_developer');

    $I->amOnPage('/node/add/stanford_page');
    $I->canSee('Layout');

    $allowed_pages = ['/admin/content'];
    $this->runAccessCheck($I, $allowed_pages);

    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->canSeeElement('#toolbar-administration');
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
  protected function runAccessCheck(AcceptanceTester $I, array $pages = [], $status_code = 200) {
    foreach ($pages as $page) {
      $I->amOnPage($page);
      $I->canSeeResponseCodeIs($status_code);
    }
  }

  /**
   * Check that the current tester can see some links.
   *
   * @param \AcceptanceTester $I
   *   Tester.
   * @param array $links
   *   Keyed array of links with the key being the path.
   * @param bool $can_see
   *   If the user can see the links or not.
   */
  protected function runLinkExistCheck(AcceptanceTester $I, array $links, $can_see = TRUE) {
    foreach ($links as $path => $link_text) {

      $path = is_int($path) ? NULL : $path;
      if ($can_see) {
        $I->canSeeLink($link_text, $path);
        continue;
      }

      $I->cantSeeLink($link_text, $path ?? '');
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
