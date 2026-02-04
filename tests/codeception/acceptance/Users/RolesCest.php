<?php

use Codeception\Attribute as CodeceptionAttribute;
use Codeception\Example;
use Faker\Factory;

/**
 * Class RolesCest.
 */
#[CodeceptionAttribute\Group('users')]
class RolesCest {

  /**
   * @var \Faker\Generator
   */
  protected $faker;

  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Default roles should exist.
   */
  public function testRolesExist(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/users/roles');
    $I->canSee('Contributor');
    $I->canSee('Site Editor');
    $I->canSee('Site Manager');
    $I->canSee('Administrator');
    $I->canSee('Site Embedder');
    $I->canSee('Site Reviewer');
  }

  /**
   * Stanford Staff role should be very limited.
   */
  public function testStaffRole(AcceptanceTester $I) {
    $I->logInWithRole('stanford_staff');
    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->cantSeeElement('#toolbar-administration');
  }

  /**
   * Stanford Staff role should be very limited.
   */
  public function testStudentRole(AcceptanceTester $I) {
    $I->logInWithRole('stanford_student');
    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->cantSeeElement('#toolbar-administration');
  }

  /**
   * Stanford Staff role should be very limited.
   */
  public function testFacultyRole(AcceptanceTester $I) {
    $I->logInWithRole('stanford_faculty');
    // D8CORE-2538 Staff and students without additional roles shouldn't see
    // the admin toolbar.
    $I->amOnPage('/');
    $I->cantSeeElement('#toolbar-administration');
  }

  /**
   * Site Reviewer role should be limited to viewing unpublished page.
   */
  public function testReviewerRole(AcceptanceTester $I) {
    $I->logInWithRole('site_reviewer');
    // D8CORE-7622
    // would be nice to have a test if they CAN see unpublished pages
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

    $I->amOnPage('/admin/patterns');
    $I->canSeeResponseCodeIs(200);
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

    $I->amOnPage('/admin/patterns');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * D8CORE-6983: Site Manager and Site embedder should play well together.
   */
  #[CodeceptionAttribute\Group('D8CORE-6983')]
  public function testSiteEmbedderStacking(AcceptanceTester $I) {
    // Site manager cannot create custom embeddables.
    $I->logInWithRole('site_manager');
    $I->amOnPage('/media/add/embeddable');
    $I->canSee('Embed Code');
    $I->fillField('Name', 'Test Embed');
    $I->fillField('Embed Code', '<div>This is an embed</div>');
    $I->click('Save');
    $I->canSee('error has been found');
    $I->amOnPage('/user/logout');
    $I->click('Log out', 'form');

    // Stack the site_embedder role.
    $user = $I->createUserWithRoles(['site_manager', 'site_embedder']);
    $I->logInAs($user->id());

    // Site managers should be able to see basic site settings.
    $I->amOnPage('/admin/config/system/basic-site-settings');
    $I->canSeeResponseCodeIs(200);
    $I->amOnPage('/media/add/embeddable');
    $I->canSee('Embed Code');

    // Site embedders can create custom embeds.
    $I->fillField('Name', 'Test Embed');
    $I->fillField('Embed Code', '<iframe src="https://calendar.google.com/foo-bar" title="foobar"></iframe>');
    $I->click('Save');
    $I->cantSee('error has been found');
    $I->canSee('Embeddable test embed has been created');
  }

  #[CodeceptionAttribute\Group('media-content')]
  #[CodeceptionAttribute\Examples(role: 'contributor', access: TRUE)]
  #[CodeceptionAttribute\Examples(role: 'site_manager', access: TRUE)]
  #[CodeceptionAttribute\Examples(role: 'administrator', access: TRUE)]
  public function testMediaContentCreateAccess(AcceptanceTester $I, Example $example) {
    $I->logInWithRole($example['role']);
    $I->amOnPage('/node/add/stanford_media');
    if ($example['access']) {
      $I->canSeeResponseCodeIs(200);
    }
    else {
      $I->canSeeResponseCodeIs(403);
    }
  }

  #[CodeceptionAttribute\Group('media-content')]
  #[CodeceptionAttribute\Examples(role: 'contributor', access: TRUE)]
  #[CodeceptionAttribute\Examples(role: 'site_manager', access: TRUE)]
  public function testMediaContentEditAccess(AcceptanceTester $I, Example $example) {
    $node = $I->createEntity([
      'type' => 'stanford_media',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $I->logInWithRole($example['role']);
    $I->amOnPage($node->toUrl('edit-form')->toString());

    if ($example['access']) {
      $I->canSeeResponseCodeIs(200);
      $I->canSeeInField('Title', $node->label());
    }
    else {
      $I->canSeeResponseCodeIs(403);
    }
  }

  #[CodeceptionAttribute\Group('media-content')]
  #[CodeceptionAttribute\Examples(role: 'contributor', access: FALSE)]
  #[CodeceptionAttribute\Examples(role: 'site_manager', access: TRUE)]
  public function testMediaTaxonomyAccess(AcceptanceTester $I, Example $example) {
    $node = $I->createEntity([
      'type' => 'stanford_media',
      'title' => $this->faker->words(3, TRUE),
    ]);
    $I->logInWithRole($example['role']);
    $I->amOnPage('/admin/structure/taxonomy');

    if ($example['access']) {
      $I->canSee('Audio/Visual Types');
      $I->canSee('Audio/Visual Content Filters');
    }
    else {
      $I->cantSee('Audio/Visual Types');
      $I->cantSee('Audio/Visual Content Filters');
    }

    foreach (['media_content_types', 'media_content_filters'] as $type) {
      $I->amOnPage("/admin/structure/taxonomy/manage/$type/overview");

      if ($example['access']) {
        $I->canSeeLink('Add term', '#taxonomy');
      }
      else {
        $I->cantSeeLink('Add term', '#taxonomy');
      }
    }
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
