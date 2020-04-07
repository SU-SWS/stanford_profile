@api
Feature: Default Content
  In order to verify that the default content is available
  As a user
  I should be able to navigate and see the default content

  Scenario: View Content
    Given I am on the homepage
    Then I should get a "200" HTTP response
    Then I should see "Welcome to your site!" in the "content" region
    And I should see 1 "meta[property='og:image'][content*='/large/']" element
    And I should see 1 "meta[property='og:image:url'][content*='/large/']" element
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

  Scenario: Media
    Given I am logged in as a user with the "site_manager" role
    And I am on "/admin/content/media"
    Then I should see 15 or more ".media-library-item img" elements
    
  Scenario: XML Sitemap Loads
    Given I run cron
    And I am on "/sitemap.xml"
    Then the response status code should be 200
