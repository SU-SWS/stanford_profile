<?php

class HomePageCest {

  public function testHomepageContent(\AcceptanceTester $I) {
    $I->amOnPage("/");
    $I->see("Cardinal Service");
  }

}
