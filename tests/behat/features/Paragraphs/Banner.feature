@api
Feature: Banner Paragraph
  In order to verify Banner is working correctly
  As a user
  I should be able to create content and it should display correctly.

  Scenario: Test Banner Paragraph.
    Given a "stanford_banner" paragraph named "created_banner":
      | su_banner_sup_header  | This is a super headline                |
      | su_banner_header      | Some Headline Here                      |
      | su_banner_button      | 0: Google Button - 1: http://google.com |
      | su_banner_body:value  | Ipsum Lorem                             |
      | su_banner_body:format | stanford_html                           |

    Then I am viewing a "stanford_page" content:
      | title              | Banner Test    |
      | su_page_components | created_banner |

    And I should see "This is a super headline" in the "content" region
    And I should see "Some Headline Here" in the "content" region
    And I should see "Ipsum Lorem" in the "content" region
    And I should see the link "Google Button"
