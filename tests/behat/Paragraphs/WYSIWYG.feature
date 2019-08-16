
Feature: WYSIWYG Paragraph
  In order to verify WYSIWYG is working correctly
  As a user
  I should be able to create content and it should be filtered correctly.

  @javascript @api
  Scenario: Test Filtered HTML.
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/stanford_page"
    Then I fill in "Title" with "Test WYSIWYG Paragraph"
    And I press "Add WYSIWYG"
    And I wait for AJAX to finish
    Then I fill in wysiwyg "Body" with "../assets/documents/WYSIWYG.html"
    Then I press "Save"
    And I should be on "/test-wysiwyg-paragraph"

    # Stripped Tags
    And I should not see "alert('testme')"
    And I should not see a "iframe" element
    And I should not see a "form" element in the "content" region
    And I should not see a "label" element in the "content" region
    And I should not see an "input" element in the "content" region
    And I should not see a "select" element in the "content" region
    And I should not see an "option" element in the "content" region
    And I should not see a "textarea" element in the "content" region
    And I should not see a "fieldset" element in the "content" region
    And I should not see a "legend" element in the "content" region
    And I should not see a "address" element in the "content" region

    # Headers
    And I should not see an "h1:contains('Level 1 heading')" element
    And I should see an "h2:contains('Level 02 Heading')" element
    And I should see an "h3:contains('Level 03 Heading')" element
    And I should see an "h4:contains('Level 04 Heading')" element
    And I should see an "h5:contains('Level 05 Heading')" element
    And I should see an "h6:contains('Level 06 Heading')" element

    # Text Tags
    And I should see an "p:contains('A small paragraph')" element
    And I should see an "a:contains('Normal Link')" element
    And I should see an "a.su-button:contains('Button')" element
    And I should see an "a.su-button--big:contains('Big Button')" element
    And I should see an "a.su-button--secondary:contains('Secondary Button')" element
    And I should see an "em:contains('emphasis')" element
    And I should see an "strong:contains('important')" element
    And I should see 1 "blockquote" element in the "content" region
    And I should not see a "footer" element in the "content" region
    And I should see 2 "code" elements in the "content" region
    And I should see 1 "dl" element in the "content" region
    And I should see 2 "dt" element in the "content" region
    And I should see 2 "dd" element in the "content" region

    # List Tags
    And I should see an "ul li:contains('This is a list')" element
    And I should see an "ul ul li:contains('child list items')" element
    And I should see an "ol li:contains('Ordered list item')" element
    And I should see an "ol ol li:contains('Child ordered list item')" element

    # Table Tags
    And I should see 1 "table" element in the "content" region
    And I should see 1 "caption" element in the "content" region
    And I should see 1 "tbody" element in the "content" region
    And I should see 3 "tr" elements in the "content" region
    And I should see 2 "th[scope]" elements in the "content" region
    And I should see 4 "td" element in the "content" region

  @api @javascript
  Scenario: Test Embedded Image
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/stanford_page"
    Then I fill in "Title" with "Test WYSIWYG Paragraph"
    And I press "Add WYSIWYG"
    And I wait for AJAX to finish
    Then I click the "a[title='Embed Media']" element
    And I wait for AJAX to finish
    Then I switch to "entity_browser_iframe_media_browser" iframe
    Then I click "Embed a File"
    And I wait for AJAX to finish
    And I wait 1 seconds
    Then I drop "../assets/images/logo.jpg" file into dropzone
    And I press "Add to Library"
    And I wait for AJAX to finish
    And I wait 1 seconds
    Then I press "Continue"
    And I wait for AJAX to finish
    And I wait 1 seconds
    Then I exit iframe
    And I wait 2 seconds
    Then I select "Medium (220×220)" from "Image Style"
    And I fill in "Alternate text" with "Stanford Logo"
    Then I click the ".entity-select-dialog .form-actions button" element
    And I wait for AJAX to finish
    Then I press "Save"
    Then I should be on "/test-wysiwyg-paragraph"
    And I should see 1 "img" element in the "content" region
    And the element "img" should have the attribute "alt" with the value "Stanford Logo"

  @api @javascript @testthis
  Scenario: Test Embedded Video
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/stanford_page"
    Then I fill in "Title" with "Test WYSIWYG Paragraph"
    And I press "Add WYSIWYG"
    And I wait for AJAX to finish
    Then I click the "a[title='Embed Media']" element
    And I wait for AJAX to finish
    Then I switch to "entity_browser_iframe_media_browser" iframe
    Then I click "Embed External Content"
    And I wait for AJAX to finish
    And I wait 1 seconds
    Then I fill in "Shareable Url" with "https://www.youtube.com/watch?v=ktCgVopf7D0"
    And I press "Add"
    And I wait for AJAX to finish
    And I wait 1 seconds
    Then I press "Continue"
    And I wait for AJAX to finish
    And I wait for AJAX to finish
    Then I exit iframe
    And I wait 2 seconds
    Then I click the ".ui-dialog button:contains('Embed')" element
    And I wait for AJAX to finish
    Then I press "Save"
    Then I should be on "/test-wysiwyg-paragraph"
    And I should see 1 "iframe" element in the "content" region
    And the element "iframe" should have the attribute "src" with the value "https://www.youtube.com/embed/ktCgVopf7D0?autoplay=0&start=0&rel=0&showinfo=1"

  @api @javascript @testthis
  Scenario: Test Embedded Document
    Given I am logged in as a user with the "administrator" role
    And I am on "/node/add/stanford_page"
    Then I fill in "Title" with "Test WYSIWYG Paragraph"
    And I press "Add WYSIWYG"
    And I wait for AJAX to finish
    Then I click the "a[title='Embed Media']" element
    And I wait for AJAX to finish
    Then I switch to "entity_browser_iframe_media_browser" iframe
    Then I click "Embed a File"
    And I wait for AJAX to finish
    And I wait 1 seconds
    Then I drop "../assets/documents/test.php" file into dropzone
    And I should see an ".dz-error.dz-complete" element
    Then I click the ".dropzonejs-remove-icon" element
    And I drop "../assets/documents/test.txt" file into dropzone
    And I press "Add to Library"
    And I wait for AJAX to finish
    And I wait 1 seconds
    And I fill in "Name" with "Test TXT Document"
    Then I press "Continue"
    And I wait for AJAX to finish
    And I wait for AJAX to finish
    Then I exit iframe
    And I wait 2 seconds
    Then the "attributes[data-entity-embed-display-settings][description]" field should contain "Test TXT Document"
    Then I click the ".ui-dialog button:contains('Embed')" element
    And I wait for AJAX to finish
    Then I press "Save"
    Then I should be on "/test-wysiwyg-paragraph"
    And I should see 1 "a[href*='.txt']:contains('Test TXT Document')" element in the "content" region
