<?php

use Codeception\Attribute as CodeceptionAttribute;
use Faker\Factory;

/**
 * Class LocalFooterCest.
 */
#[CodeceptionAttribute\Group('local-footer')]
class LocalFooterCest {

  /**
   * Faker service.
   *
   * @var \Faker\Generator
   */
  protected $faker;

  public function __construct() {
    $this->faker = Factory::create();
  }

  /**
   * Tidy up after oneself.
   */
  public function _after(AcceptanceTester $I) {
    $config_page = \Drupal::entityTypeManager()
      ->getStorage('config_pages')
      ->load('stanford_local_footer');
    if ($config_page) {
      $config_page->delete();
    }
  }

  #[CodeceptionAttribute\Group('login-link')]
  public function testLoginLinkDestination(AcceptanceTester $I) {
    $link_text = $this->faker->word();
    $I->runDrush('config:pages-set-field-value stanford_local_footer su_footer_enabled 1');
    $I->runDrush("config:pages-set-field-value stanford_local_footer su_local_foot_sunet_t $link_text");
    $I->runDrush('cr');

    for ($i = 0; $i < 5; $i++) {
      $page = $I->createEntity([
        'type' => 'stanford_page',
        'title' => $this->faker->slug(),
      ]);
      $destination = $page->toUrl()->toString();
      $query = $this->faker->slug();
      $key = strtolower($this->faker->word());

      $I->amOnPage("$destination?$key=/$query");
      $I->canSeeLink($link_text, "/saml/login?destination=$destination");
    }
    for ($i = 0; $i < 5; $i++) {
      $destination = $this->faker->slug();
      $query = $this->faker->slug();
      $key = strtolower($this->faker->word());

      $I->amOnPage("$destination?$key=/$query");
      $I->canSeeResponseCodeIs(404);
      $I->canSeeLink($link_text, '/saml/login?destination=/');
    }
  }

  /**
   * Only site manager and higher should have access.
   */
  public function testAccess(AcceptanceTester $I) {
    $I->logInWithRole('contributor');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(403);
    $I->amOnPage('/user/logout');
    $I->click('Log out', 'form');
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->canSeeResponseCodeIs(200);
  }

  /**
   * Changes to the local footer should display correctly.
   */
  #[CodeceptionAttribute\Group('social-links')]
  public function testCustomLocalFooter(AcceptanceTester $I) {
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->checkOption('Enabled');
    $I->selectOption('Country', 'United States');
    $I->click('Save');
    $I->see('Local Footer has been', '.messages-list');

    $I->selectOption('State', 'New York');
    $fields = [
      'Organization' => 'Drupal',
      'Street address' => '123 Drupal Dr',
      'City' => 'New York',
      'Zip code' => 12345,
      'su_local_foot_action[0][uri]' => 'http://google.com',
      'su_local_foot_action[0][title]' => 'Action Link',
      'su_local_foot_social[0][uri]' => 'http://foobar.com',
      'su_local_foot_social[0][title]' => 'Facebook Social Link',
      'Primary Links Header' => 'Primary links header',
      'su_local_foot_primary[0][uri]' => 'http://google.com',
      'su_local_foot_primary[0][title]' => 'Primary Link',
      'Secondary Links Header' => 'Secondary Links Header',
      'su_local_foot_second[0][uri]' => 'http://google.com',
      'su_local_foot_second[0][title]' => 'Secondary Link',
      'su_local_foot_f_intro[0][value]' => '<p>Lorem Ipsum</p>',
      'Form Action URL' => 'http://google.com',
      'Signup Button Text' => 'Sign Me Up',
    ];

    foreach ($fields as $selector => $value) {
      $I->fillField($selector, $value);
    }

    $I->click('Save');

    $I->canSee('Social URL is not supported.');
    $I->fillField('su_local_foot_social[0][uri]', 'http://facebook.com');
    $I->click('Save');

    $I->see('Local Footer has been', '.messages-list');

    $I->amOnPage('/');
    $I->canSee('123 Drupal Dr');
    $I->canSee('New York, NY 12345');
    $I->canSeeLink('Action Link', 'http://google.com');
    $I->canSeeLink('Facebook Social Link', 'http://facebook.com');
    $I->canSee('Primary links header', 'h2');
    $I->canSeeLink('Primary Link', 'http://google.com');
    $I->canSee('Secondary Links Header', 'h2');
    $I->canSeeLink('Secondary Link', 'http://google.com');
    $I->canSee('Lorem Ipsum', 'p');
    $I->canSeeElement('input[value="Sign Me Up"]');

    $I->amOnPage('/admin/config/system/local-footer');
    $I->uncheckOption('Enabled');
    $I->click('Save');
    $I->see('Local Footer has been', '.messages-list');

    $I->amOnPage('/');
    $I->cantSee('123 Drupal Dr');
  }

  /**
   * Content blocks.
   */
  public function testCustomContentLocalFooter(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->checkOption('Enabled');
    $I->fillField("#edit-su-local-foot-pr-co-0-value", "<p>Block one</p>");
    $I->fillField("#edit-su-local-foot-se-co-0-value", "<p>Block two</p>");
    $I->fillField("#edit-su-local-foot-tr-co-0-value", "<p>Block three</p>");
    $I->click('Save');
    $I->see('Local Footer has been', '.messages-list');

    $I->amOnPage('/');
    $I->canSee('Block one');
    $I->canSee('Block two');
    $I->canSee('Block three');
  }

  /**
   * Route urls and no link urls should function correctly in the footer.
   */
  public function testNodeRoutesAndNoLink(AcceptanceTester $I) {
    $node = $I->createEntity([
      'type' => 'stanford_page',
      'title' => 'Test Page',
    ]);
    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/system/local-footer');
    $I->checkOption('Enabled');
    $I->fillField('su_local_foot_primary[0][uri]', $node->label() . " ({$node->id()})");
    $I->fillField('su_local_foot_primary[0][title]', $node->label());
    $I->click('Save');
    $I->see('Local Footer has been', '.messages-list');

    $I->amOnPage('/');
    $I->canSeeLink($node->label(), $node->toUrl()->toString());

    $I->amOnPage('/admin/config/system/local-footer');
    $I->checkOption('Enabled');
    $I->fillField('su_local_foot_primary[0][uri]', '<nolink>');
    $I->fillField('su_local_foot_primary[0][title]', 'NO LINK');
    $I->click('Save');
    $I->see('Local Footer has been', '.messages-list');

    $I->amOnPage('/');
    $I->canSee('NO LINK', 'li span');
  }

}
