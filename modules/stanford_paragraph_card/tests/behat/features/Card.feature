@api
Feature: Card Paragraph
  In order to verify Card is working correctly
  As a user
  I should be able to create content and it should display correctly.

  Scenario: Test Card Paragraph.
    Given a "stanford_card" paragraph named "created_card":
      | su_card_super_header | Some Headline Here                      |
      | su_card_header       | This is a sub headline                  |
      | su_card_link         | 0: Google Button - 1: http://google.com |
      | su_card_body:value   | Ipsum Lorem                             |
      | su_card_body:format  | stanford_html                           |

    Then I am viewing a "stanford_page" content:
      | title              | Card Test    |
      | su_page_components | created_card |

    And I should see "This is a sub headline" in the "content" region
    And I should see "Some Headline Here" in the "content" region
    And I should see "Ipsum Lorem" in the "content" region
    And I should see the link "Google Button"

  Scenario: Test Button Display.
    Given a "stanford_card" paragraph named "created_card":
      | su_card_super_header | Some Headline Here                    |
      | su_card_header       | This is a sub headline                |
      | su_card_link         | 0: Action Link - 1: http://google.com |
      | su_card_body:value   | Ipsum Lorem                           |
      | su_card_body:format  | stanford_html                         |
      | su_card_link_display | action                                |

    Then I am viewing a "stanford_page" content:
      | title              | Card Test    |
      | su_page_components | created_card |

    And I should see "This is a sub headline" in the "content" region
    And I should see "Some Headline Here" in the "content" region
    And I should see "Ipsum Lorem" in the "content" region
    And I should see the link "Action Link"
    And I should see 1 ".su-link--action" element in the "content" region
