@api
Feature: Local Footer
  In order to verify that the Local footer is displayed
  As a site manager
  I should be edit the local footer fields

  Scenario: Access Denied to edit local footer
    Given I am logged in as a user with the "contributor" role
    And I am on "/admin/appearance/local-footer"
    Then the response status code should be 403

  @javascript
  Scenario: Site manager can change the footer
    Given I am logged in as a user with the "site_manager" role
    And I am on "/admin/appearance/local-footer"
    And I check "Enabled"
    Then I click the "summary:contains('Address')" element
    And I select "New York" from "State"
    And I fill in the following:
      | Company        | Drupal        |
      | Street address | 123 Drupal Dr |
      | City           | New York      |
      | Zip code       | 12345         |
    Then I click the "summary:contains('Action and Social Links')" element
    And I fill in the following:
      | su_local_foot_action[0][uri]    | http://google.com    |
      | su_local_foot_action[0][title]  | Action Link          |
      | su_local_foot_social[0][uri]    | http://facebook.com  |
      | su_local_foot_social[0][title]  | Facebook Social Link |
    Then I click the "summary:contains('Primary Links')" element
    And I fill in the following:
      | Primary Links Header             | Primary links header |
      | su_local_foot_primary[0][uri]    | http://google.com    |
      | su_local_foot_primary[0][title]  | Primary Link         |
    Then I click the "summary:contains('Secondary Links')" element
    And I fill in the following:
      | Secondary Links Header          | Secondary Links Header |
      | su_local_foot_second[0][uri]    | http://google.com      |
      | su_local_foot_second[0][title]  | Secondary Link         |
    Then I click the "summary:contains('Signup Form')" element
    And I fill in wysiwyg "Signup Form Intro" with "Lorem Ipsum"
    And I fill in the following:
      | Form Action URL    | http://google.com  |
      | Signup Button Text | Sign Me up         |
    And I press "Save"
    Then the cache has been cleared
    And I am on the homepage
    Then I should see "123 Drupal Dr"
    And I should see "New York, NY 12345"
    And I should see the link "Action Link"
    And I should see the link "Facebook Social Link"
    And I should see the heading "Primary links header"
    And I should see the link "Primary Link"
    And I should see the heading "Secondary Links Header"
    And I should see the link "Secondary Link"
    And I should see "Lorem Ipsum"
    And I should see the button "Sign Me up"
    Then I am on "/admin/appearance/local-footer"
    And I uncheck "Enabled"
    And I press "Save"
    Then the cache has been cleared
    And I am on the homepage
    And I should not see "123 Drupal Dr"
