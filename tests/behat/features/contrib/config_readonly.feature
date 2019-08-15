Feature: Contrib - config_readonly
  In order to verify that contrib module config_readonly is working
  As an administrative user
  I should be not be able to administer settings when the module is active

  @api
  Scenario: Ensure no configuration changes are possible through the UI.
    Given I am logged in as a user with the "Administrator" role
    Then check that the "config_readonly" module is enabled
