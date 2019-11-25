@api
Feature: Default Content
  In order to verify that the default content is available
  As a user
  I should be able to navigate and see the default content

  Scenario: View Content
  Given I am on the homepage
  Then I should get a "200" HTTP response
  Then I should see "Welcome to your site!" in the "content" region
  Then I should get a "200" HTTP response
  Then I am on "/resources"
  Then I should get a "200" HTTP response
  Then I am on "/research"
  Then I should get a "200" HTTP response
  Then I am on "/about"
  Then I should get a "200" HTTP response
  Then I am on "/page-not-found"
  Then I should get a "200" HTTP response
  Then I am on "/access-denied"
  Then I should get a "200" HTTP response
