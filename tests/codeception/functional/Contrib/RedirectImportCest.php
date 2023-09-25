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
    if (file_exists(codecept_data_dir('/redirects.csv'))) {
      unlink(codecept_data_dir('/redirects.csv'));
    }
  }

  /**
   * An imported redirect csv will create the redirects we need.
   */
  public function testRedirectImports(FunctionalTester $I) {
    if (!file_exists(codecept_data_dir())) {
      mkdir(codecept_data_dir());
    }

    $file = fopen(codecept_data_dir('redirects.csv'), 'w+');
    fputcsv($file, ['source', 'destination', 'language', 'status_code']);
    fputcsv($file, ['/foo', '/bar', 'und', 301]);
    fclose($file);

    $I->logInWithRole('site_manager');
    $I->amOnPage('/admin/config/search/redirect/migrate');
    $I->attachFile('CSV File', 'redirects.csv');
    $I->waitForElementVisible('.form-managed-file input[type="submit"]', 10);

    $I->click('Migrate data');
    $I->waitForText('Processed 1 item');
    $I->amOnPage('/admin/config/search/redirect');
    $I->canSee('/foo');
    $I->amOnPage('/foo');
    $current_url = $I->grabFromCurrentUrl();
    $I->assertEquals('/bar', $current_url, 'URLS do not match');
  }

}
