@api
Feature: Layout Builder Paragraphs
  In order to customize the layout
  As a site manager
  I should be able to create custom blocks from within layout builder.

  @testthis
  Scenario: Create Paragraphs In Layout Builder
    Given I am logged in as a user with the "site_manager" role
    And I am viewing a "stanford_page" with the title "Customized Layout Builder"
    Then I click "Layout"
    And I click "Add block"
    Then I click "Create custom block"
    And I fill in "Title" with "custom block"
    And I fill in "Body" with "Lorem Ipsum Custom Block Text"
    And I press "Add block"
    Then I press "Save layout"
    And I should see "Lorem Ipsum Custom Block Text"
