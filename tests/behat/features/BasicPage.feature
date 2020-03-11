@api
Feature: Basic Page
  In order to verify that Basic Page is working
  As a user
  I should be create a basic page

  Scenario: Create a simple basic page.
    Given I am logged in as a user with the "Contributor" role
    And I am on "node/add/stanford_page"
    Then the response status code should be 200
    And I fill in "Title" with "Behat Test Page"
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
    Then I check "Provide a menu link"
    And I fill in "Menu link title" with "Another Behat Test Menu Item"
    And I select "-- Behat Test Page Menu Item" from "Parent item"
    Then I press "Change parent (update list of weights)"
    And I press "Save"
    Then I should see "Another Behat Test Menu Item" in the "menu" region

  Scenario: Count the number of H1 Tags
    Given I am on "/this-doesnt-exist"
    Then I should see 1 "h1" element
    And the response status code should be 404
    And I am on "/search/content?keys=stuff&search="
    Then I should see 1 "h1" element
    And the response status code should be 200

  # Regression test for: D8CORE-1547
  Scenario: Access the revisions page
    Given I am logged in as a user with the "Site Manager" role
    Given I am viewing a "stanford_page" with the title "I would like revisions"
    Then I am on "/i-would-like-revisions"
    Then I click "Revisions"
    And the response status code should be 200
