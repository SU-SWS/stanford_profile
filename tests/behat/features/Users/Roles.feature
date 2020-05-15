@api
Feature: Roles
  In order to verify that the correct roles are installed
  As an administrative user
  I should be able to log in and review the roles exist.

  Scenario: Check roles exist
    Given I am logged in as a user with the "Administrator" role
    And I am on "admin/people/roles"
    Then I should see "Contributor"
    Then I should see "Site Editor"
    Then I should see "Site Manager"
    Then I should see "Site Builder"
    Then I should see "Site Developer"
    Then I should see "Administrator"

  Scenario: Check I can log in as the Contributor role
    Given I am logged in as a user with the "Contributor" role
    And I am on "admin/content"
    Then I should get a "200" HTTP response
    And I am on "/node/add/stanford_page"
    And I should not see "Layout"
    Then I am on "/node/11/delete"
    And I should get a 403 HTTP response

  Scenario: Check I can log in as the Site Editor role
    Given I am logged in as a user with the "Site Editor" role
    And I am on "admin/content"
    Then I should get a "200" HTTP response
    And I am on "/node/add/stanford_page"
    And I should not see "Layout"
    Then I am on "/node/11/delete"
    And I should get a 403 HTTP response

  Scenario: Check I can log in as the Site Manager role
    Given I am logged in as a user with the "Site Manager" role
    And I am on "admin/content"
    Then I should get a "200" HTTP response
    And I am on "/node/add/stanford_page"
    And I should see "Layout"
    Then I am on "/node/11/delete"
    And I should get a 403 HTTP response
    Then I am on "/admin/content/media"
    And I should get a 200 HTTP response

  Scenario: Check I can log in as the Site Builder role
    Given I am logged in as a user with the "Site Builder" role
    And I am on "admin/content"
    Then I should get a "200" HTTP response
    And I am on "/node/add/stanford_page"
    And I should see "Layout"
    Then I am on "/node/11/delete"
    And I should get a 200 HTTP response

  Scenario: Check I can log in as the Site Developer role
    Given I am logged in as a user with the "Site Developer" role
    And I am on "admin/content"
    Then I should get a "200" HTTP response
    And I am on "/node/add/stanford_page"
    And I should see "Layout"
    Then I am on "/node/11/delete"
    And I should get a 200 HTTP response
