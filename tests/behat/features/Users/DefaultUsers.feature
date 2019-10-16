@api
Feature: Default Users
  In order to verify that the default users are available
  As an administrative user
  I should be able to see a list of users on the people page

  Scenario: Check default users exist
    Given I am logged in as a user with the "Administrator" role
    And I am on "admin/people"
    Then I should see "Alex"
    Then I should see "Jamie"
    Then I should see "Sam"
    Then I should see "Morgan"
    Then I should see "Kennedy"
