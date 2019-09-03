Feature: Basic Page
  In order to verify that Basic Page is working
  As a user
  I should be create a basic page

  @api
  Scenario: Create a simple basic page.
    Given I am logged in as a user with the "Administrator" role
    And I am on "node/add/stanford_page"
    Then the response status code should be 200
    And I fill in "Title" with "Behat Test Page"
    And I fill in "Body" with "Lorem Ipsum"
    Then I check "Provide a menu link"
    And I fill in "Menu link title" with "Behat Test Page Menu Item"
    And I select "<Main navigation>" from "Parent item"
    And I press "Save"
    Then I should be on "/behat-test-page"
    And I should see "Behat Test Page Menu Item" in the "menu" region
    And I should see "Behat Test Page"
    # Child Page
    Then I am on "node/add/stanford_page"
    And I fill in "Title" with "Another Behat Test"
    Then I fill in "Body" with "Lorem Ipsum"
    Then I check "Provide a menu link"
    And I fill in "Menu link title" with "Another Behat Test Menu Item"
    And I select "-- Behat Test Page Menu Item" from "Parent item"
    And I press "Save"
    Then I should see "Another Behat Test Menu Item" in the "menu" region
