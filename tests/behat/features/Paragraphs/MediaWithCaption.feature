@api
Feature: Media Paragraph
  In order to verify Media with Caption is working correctly
  As a user
  I should be able to create content and it should display correctly.

  Scenario: Test Banner Paragraph.
    Given I am logged in as a user with the "contributor" role
    And I am on "/node/add/stanford_page"
    Then I fill in "Title" with "Test Behat Media Caption"
    And I fill in "Body" with "Lorem Ipsum"
    Then I press "Media with Caption"
    And I fill in "su_page_components[0][subform][su_media_caption_caption][0][value]" with "Hello, I am a caption."
    And I fill in "Link" with "http://google.com"
    Then I press "Save"
    And I should be on "/test-behat-media-caption"
    And I should see "Hello, I am a caption" in the "content" region
