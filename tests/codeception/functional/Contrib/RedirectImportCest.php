<?php

/**
 * Class RedirectImportCest.
 *
 * @group contrib
 */
class RedirectImportCest {

  /**
   * Cleanup.
   */
  public function __after() {
    if (file_exists(rtrim(codecept_data_dir(), '/') . '/redirects.csv')) {
      unlink(rtrim(codecept_data_dir(), '/') . '/redirects.csv');
    }
  }

  /**
   * An imported redirect csv will create the redirects we need.
   */
  public function testRedirectImports(FunctionalTester $I) {

    $file = fopen(rtrim(codecept_data_dir(), '/') . '/redirects.csv', 'w+');
    fputcsv($file, ['from', 'to']);
    fputcsv($file, ['/foo', '/bar']);
    fclose($file);

    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/search/redirect/import');
    $I->attachFile('CSV File', 'redirects.csv');
    $I->checkOption('Allow nonexistent paths to be imported');
    $I->click('Import', '.redirect-import-form');
    $I->waitForText('Redirects processed');
    $I->amOnPage('/admin/config/search/redirect');
    $I->canSee('/foo');
    $I->amOnPage('/foo');
    $current_url = $I->grabFromCurrentUrl();
    $I->assertEquals('/bar', $current_url, 'URLS do not match');
  }

}
