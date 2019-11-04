@api
Feature: Card Paragraph
  In order to verify Card is working correctly
  As a user
  I should be able to create content and it should display correctly.

  Scenario: Test Card Paragraph.
    Given I am logged in as a user with the "contributor" role
    And I am on "/node/add/stanford_page"
    Then I fill in "Title" with "Test Behat"
    And I fill in "Body" with "Lorem Ipsum"
    Then I press "Card"
    And I fill in "Subhead" with "This is a sub headline"
    And I fill in "Headline" with "Some Headline Here"
    And I fill in "su_page_components[1][subform][su_card_body][0][value]" with "Ipsum Lorem"
    And I fill in "URL" with "http://google.com"
    And I fill in "Link text" with "Google Button"
    Then I press "Save"
    And I should be on "/test-behat"
    And I should see "This is a sub headline" in the "content" region
    And I should see "Some Headline Here" in the "content" region
    And I should see "Ipsum Lorem" in the "content" region
    And I should see the link "Google Button"

  Scenario: Test Button Display.
    Given I am logged in as a user with the "contributor" role
    And I am on "/node/add/stanford_page"
    Then I fill in "Title" with "Button Display Test"
    And I fill in "Body" with "Lorem Ipsum"
    Then I press "Card"
    And I fill in "Subhead" with "This is a sub headline"
    And I fill in "Headline" with "Some Headline Here"
    And I fill in "su_page_components[1][subform][su_card_body][0][value]" with "Ipsum Lorem"
    And I fill in "URL" with "http://google.com"
    And I fill in "Link text" with "Action Link"
    Then I select "action" from "Link Display"
    Then I press "Save"
    And I should be on "/button-display-test"
    And I should see "This is a sub headline" in the "content" region
    And I should see "Some Headline Here" in the "content" region
    And I should see "Ipsum Lorem" in the "content" region
    And I should see the link "Action Link"
    And I should see 1 ".su-link--action" element in the "content" region
