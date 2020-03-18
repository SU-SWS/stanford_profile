@api
Feature: Flush Caches
  In order to verify that certain roles can flush caches
  As a site manager
  I should be able to flush caches through the ui

  @javascript
  Scenario: Create a simple basic page.
    Given I am logged in as a user with the "Site Manager" role
    Then I click on the "Flush all caches" link
    Then I wait for AJAX to finish
    Then I should see "All caches cleared."
