@api
Feature: Media Paragraph
  In order to verify Media with Caption is working correctly
  As a user
  I should be able to create content and it should display correctly.

  Scenario: Test Banner Paragraph.
    Given I am logged in as a user with the "contributor" role
    Given a "stanford_media_caption" paragraph named "created_media_caption":
      | su_media_caption_caption   | This is a super caption                 |
      | su_media_caption_link      | 0: Google Button - 1: http://google.com |

    Then I am viewing a "stanford_page" content:
      | title              | Media Caption Test    |
      | su_page_components | created_media_caption |

    And I should see "This is a super caption" in the "content" region