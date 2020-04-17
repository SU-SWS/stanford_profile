<?php

/**
 * Test the basic page components.
 */
class BasicPageComponentsCest {

  /**
   * Validate the Spacer Paragraph type exists
   */
  public function testSpacerParagraph(AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/paragraphs_type');
    $I->canSee('Spacer');
    $I->canSee("stanford_spacer");
  }

}
