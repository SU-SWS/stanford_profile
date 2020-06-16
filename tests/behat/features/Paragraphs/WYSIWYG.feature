Feature: WYSIWYG Paragraph
  In order to verify WYSIWYG is working correctly
  As a user
  I should be able to create content and it should be filtered correctly.

  @api @javascript
  Scenario: Test Embedded Image
    Given I am logged in as a user with the "administrator" role
    Given a "stanford_wysiwyg" paragraph named "created_text":
      | su_wysiwyg_text:value  | Ipsum Lorem   |
      | su_wysiwyg_text:format | stanford_html |

    Then I am viewing a "stanford_page" content:
      | title              | Banner Test  |
      | su_page_components | created_text |
    Then I should see 0 "img" element in the "content" region
    And I click the ".local-tasks-block a:contains('Edit')" element
    And I set the window size to "extra large"
    And I wait 1 seconds
    Then I click the ".inner-row-wrapper button" element
    And I wait 1 seconds
    Then I click the "a[title='Insert from Media Library']" element
    And I wait for element ".dropzone"
    Then I drop "../../assets/images/logo.jpg" file into dropzone
    And I press "Upload and Continue"
    And I wait for element "input[name*='alt']"
    And I fill in "Alternative text" with "Stanford Logo"
    Then I click the ".ui-dialog-buttonset button:contains('Save and insert')" element
    And I wait for AJAX to finish
    And I wait 1 seconds
    Then I press "Continue"
    Then I wait for element ".MuiDialog-scrollPaper" to be gone
    Then I press "Save"
    And I should see 1 "img" element in the "content" region


  @api @javascript
  Scenario: Test Embedded Document
    Given I am logged in as a user with the "administrator" role
    Given a "stanford_wysiwyg" paragraph named "created_text":
      | su_wysiwyg_text:value  | Ipsum Lorem   |
      | su_wysiwyg_text:format | stanford_html |

    Then I am viewing a "stanford_page" content:
      | title              | Banner Test  |
      | su_page_components | created_text |
    Then I should see 0 "img" element in the "content" region
    And I click the ".local-tasks-block a:contains('Edit')" element
    And I set the window size to "extra large"
    And I wait 1 seconds
    Then I click the ".inner-row-wrapper button" element
    And I wait 1 seconds
    Then I click the "a[title='Insert from Media Library']" element
    And I wait for element ".dropzone"
    Then I click the "a[data-title='File']" element
    And I wait for AJAX to finish
    Then I drop "../../assets/documents/test.php" file into dropzone
    And I should see an ".dz-error.dz-complete" element
    Then I click the ".dropzonejs-remove-icon" element
    And I drop "../../assets/documents/test.txt" file into dropzone
    And I press "Upload and Continue"
    And I wait for AJAX to finish
    And I wait 1 seconds
    And I fill in "Name" with "Test TXT Document"
    Then I click the ".ui-dialog-buttonset button:contains('Save and insert')" element
    And I wait for AJAX to finish
    And I wait 1 seconds
    Then I press "Continue"
    Then I wait for element ".MuiDialog-scrollPaper" to be gone
    Then I press "Save"
    And I should see 1 "a[href*='.txt']:contains('Test TXT Document')" element in the "content" region
