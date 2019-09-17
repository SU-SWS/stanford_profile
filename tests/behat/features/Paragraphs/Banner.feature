@api
Feature: Banner Paragraph
  In order to verify Banner is working correctly
  As a user
  I should be able to create content and it should display correctly.

  Scenario: Test Banner Paragraph.
    Given I am logged in as a user with the "contributor" role
    And I am on "/node/add/stanford_page"
    Then I fill in "Title" with "Test Behat"
    And I fill in "Body" with "Lorem Ipsum"
    Then I press "Banner"
    And I fill in "Super headline" with "This is a super headline"
    And I fill in "Headline" with "Some Headline Here"
    And I fill in "su_page_components[1][subform][su_banner_body][0][value]" with "Ipsum Lorem"
    And I fill in "URL" with "http://google.com"
    And I fill in "Link text" with "Google Button"
    Then I press "Save"
    And I should be on "/test-behat"
    And I should see "This is a super headline" in the "content" region
    And I should see "Some Headline Here" in the "content" region
    And I should see "Ipsum Lorem" in the "content" region
    And I should see the link "Google Button"
