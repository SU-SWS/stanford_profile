@api
Feature: Text formats
  In order to verify that Configuration is accurate
  As a administrator
  I should check various text format configuration settings.

  @testthis
  Scenario: Basic HTML
    Given I am logged in as a user with the "administrator" role
    And I am on "/admin/config/content/formats/manage/stanford_html"
    Then the checkbox "Large (480px wide, un-cropped)" should be checked
    And the checkbox "Large square (480px, cropped)" should be checked
    And the checkbox "Medium (220px wide, un-cropped)" should be checked
    And the checkbox "Medium square (220px, cropped)" should be checked
    And the checkbox "Circle headshot (112px, cropped)" should be checked
    And the checkbox "Thumb (100px, cropped)" should be checked
    And the checkbox "Thumb (100px wide, un-cropped)" should be checked
    And the checkbox "Full content" should not be checked
    And the checkbox "Media library" should not be checked
    Then I am on "/admin/structure/media/manage/image/display/stanford_image_large"
    And I should see "Image style: Large (480 wide)"
    Then I am on "/admin/structure/media/manage/image/display/stanford_image_large_square"
    And I should see "Image style: Large Square (480x480)"
    Then I am on "/admin/structure/media/manage/image/display/stanford_image_medium"
    And I should see "Image style: Medium (220 wide)"
    Then I am on "/admin/structure/media/manage/image/display/stanford_image_medium_square"
    And I should see "Image style: Medium Square (220x220)"
    Then I am on "/admin/structure/media/manage/image/display/stanford_image_stanford_circle"
    And I should see "Image style: Circle"
    Then I am on "/admin/structure/media/manage/image/display/stanford_image_thumb_square"
    And I should see "Image style: Thumbnail Square (100x100)"
    Then I am on "/admin/structure/media/manage/image/display/thumbnail"
    And I should see "Image style: Thumbnail (100 wide)"
