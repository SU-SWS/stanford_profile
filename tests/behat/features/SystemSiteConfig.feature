@api
Feature: System Site Config
  In order to verify that Configuration is overridden
  As a user
  I should be able to change site settings.

  Scenario: Set basic site settings
    Given I am logged in as a user with the "site_manager" role
    And I am on the homepage
    Then I should not see "Foo Bar Site"
    And I am on "/admin/config/system/basic-site-settings"
    Then I fill in "Site Name" with "Foo Bar Site"
    And I fill in "Google Analytics Account" with "UA-123456-12"
    And I press "Save"
    Then I am on the homepage
    And I should see "Foo Bar Site"
    And I should see "UA-123456-12"
    Then I am on "/admin/config/system/basic-site-settings"
    And I fill in "Site Name" with ""
    And I fill in "Google Analytics Account" with ""
    And I press "Save"
    Then I am on the homepage
    And I should not see "Foo Bar Site"
