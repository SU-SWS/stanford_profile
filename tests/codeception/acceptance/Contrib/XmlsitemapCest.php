<?php

/**
 * Test that stanford_profile_xmlsitemap_link_alter is working.
 */
class XmlsitemapCest {

  /**
   * Test that xmlsitemap is having 403 and 404 pages removed.
   */
  public function testStanfordProfileSitemapLinkAlter(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage("/admin/config/search/xmlsitemap/rebuild");
    $I->click('Save configuration');
    $I->runDrush('cr');
    $I->amOnPage("/sitemap.xml");
    $host = \Drupal::request()->getSchemeAndHttpHost();
    /** @var \Drupal\path_alias\AliasManagerInterface $alias_manager */
    $alias_manager = \Drupal::service('path_alias.manager');

    $soe_profile_403_page = \Drupal::config('system.site')->get('page.403');
    $alias_403 = $alias_manager->getAliasByPath($soe_profile_403_page);
    $soe_profile_404_page = \Drupal::config('system.site')->get('page.404');
    $alias_404 = $alias_manager->getAliasByPath($soe_profile_404_page);
    $I->dontSeeLink($host . $alias_403);
    $I->dontSeeLink($host . $alias_404);
  }

}
