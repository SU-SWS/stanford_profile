<?php

/**
 * @file
 * stanford_profile.profile
 */

use Drupal\config_pages\Entity\ConfigPages;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Implements hook_install_tasks().
 */
function stanford_profile_install_tasks(&$install_state) {
  return ['stanford_profile_final_task' => []];
}

/**
 * Perform final tasks after the profile has completed installing.
 *
 * @param array $install_state
 *   Current install state.
 */
function stanford_profile_final_task(array &$install_state) {
  \Drupal::service('plugin.manager.install_tasks')->runTasks($install_state);
}

/**
 * Implements hook_preprocess().
 */
function stanford_profile_preprocess(array &$variables, $hook) {

  // The default is to use the theme logo,
  // so this sets the variable that the twig template uses.
  // If the config page has not been saved, this sets the default setting.
  $variables['su_use_theme_logo'] = '1';
  $myConfigPage = ConfigPages::config('lockup_settings');
  if (isset($myConfigPage)) {
    $variables['su_use_theme_logo'] = $myConfigPage->get('su_use_theme_logo')->value;
    if ($variables['su_use_theme_logo'] == '0') {
      $variables['su_path_to_custom_logo'] = $myConfigPage->get('su_path_to_custom_logo')->value;
    }
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 */
function stanford_profile_form_config_pages_lockup_settings_form_alter(array &$form, FormStateInterface $form_state) {

  $img = '<img src="' . base_path() . drupal_get_path('theme', 'stanford_basic') . '/dist/assets/img/lockup-example.png" />';
  $decanter = Link::fromTextAndUrl(t('Decanter Lockup Component'), Url::fromUri('https://decanter.stanford.edu/component/identity-lockup/'))->toString();
  $form['group_lockup_options']['#field_prefix'] = "<p>$img</p><p>More examples can be found at: $decanter</p>";

  // Hide path and upload if using theme logo.
  $form['su_path_to_custom_logo']['#states'] = [
    'invisible' => [
      ':input[name="su_use_theme_logo[value]"]' => ['checked' => TRUE],
    ],
    'visible' => [
      ':input[name="su_use_theme_logo[value]"]' => ['checked' => FALSE],
    ],
  ];

  $form['su_upload_logo_image']['#states'] = [
    'invisible' => [
      ':input[name="su_use_theme_logo[value]"]' => ['checked' => TRUE],
    ],
    'visible' => [
      ':input[name="su_use_theme_logo[value]"]' => ['checked' => FALSE],
    ],
  ];

}
