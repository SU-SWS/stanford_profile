<?php

/**
 * Tests for various media access functionality.
 */
class MediaPermissionsCest {

  /**
   * Documents can be embedded as links.
   */
  public function testFileLinks(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
  }

}
