@api
Feature: Flush Caches
  In order to verify that certain roles can flush caches
  As a site manager
  I should be able to flush caches through the ui

  Scenario: Site manager flush cache.
    Given I am logged in as a user with the "Site Manager" role
    Then I click "Flush all caches"
    Then I should see "All caches cleared."

  Scenario: Contributor no flush cache.
    Given I am logged in as a user with the "Contributor" role
    Then I should not see "Flush all caches"

  Scenario: Editor no flush cache.
    Given I am logged in as a user with the "Site Editor" role
    Then I should not see "Flush all caches"
