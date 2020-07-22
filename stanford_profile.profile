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
 * Implements hook_form_FORM_ID_alter().
 */
function stanford_profile_form_config_pages_lockup_settings_form_alter(array &$form, FormStateInterface $form_state) {

  $img = '<img src="' . base_path() . drupal_get_path('theme', 'stanford_basic') . '/dist/assets/img/lockup-example.png" />';
  $decanter = Link::fromTextAndUrl(t('Decanter Lockup Component'), Url::fromUri('https://decanter.stanford.edu/component/identity-lockup/'))->toString();
  $form['group_lockup_options']['#field_prefix'] = "<p>$img</p><p>More examples can be found at: $decanter</p>";

  $form['su_upload_logo_image']['#states'] = [
    'invisible' => [
      ':input[name="su_use_theme_logo[value]"]' => ['checked' => TRUE],
    ],
    'visible' => [
      ':input[name="su_use_theme_logo[value]"]' => ['checked' => FALSE],
    ],
  ];

  $form['su_line_1']['widget']['0']['value']['#states'] = [
    'invisible' => [
      [':input[name="su_lockup_options"]' => ['value' => 'none']],
      [':input[name="su_lockup_options"]' => ['value' => 'o']],
      [':input[name="su_lockup_options"]' => ['value' => 'r']],
    ],
  ];

  $form['su_line_2']['widget']['0']['value']['#states'] = [
    'invisible' => [
      [':input[name="su_lockup_options"]' => ['value' => 'none']],
      [':input[name="su_lockup_options"]' => ['value' => 'a']],
      [':input[name="su_lockup_options"]' => ['value' => 'd']],
      [':input[name="su_lockup_options"]' => ['value' => 'h']],
      [':input[name="su_lockup_options"]' => ['value' => 'i']],
      [':input[name="su_lockup_options"]' => ['value' => 'k']],
      [':input[name="su_lockup_options"]' => ['value' => 'l']],
      [':input[name="su_lockup_options"]' => ['value' => 'n']],
      [':input[name="su_lockup_options"]' => ['value' => 'o']],
      [':input[name="su_lockup_options"]' => ['value' => 'p']],
      [':input[name="su_lockup_options"]' => ['value' => 'q']],
      [':input[name="su_lockup_options"]' => ['value' => 'r']],
    ],
  ];

  $form['su_line_3']['widget']['0']['value']['#states'] = [
    'invisible' => [
      [':input[name="su_lockup_options"]' => ['value' => 'none']],
      [':input[name="su_lockup_options"]' => ['value' => 'a']],
      [':input[name="su_lockup_options"]' => ['value' => 'b']],
      [':input[name="su_lockup_options"]' => ['value' => 'c']],
      [':input[name="su_lockup_options"]' => ['value' => 'f']],
      [':input[name="su_lockup_options"]' => ['value' => 'g']],
      [':input[name="su_lockup_options"]' => ['value' => 'j']],
      [':input[name="su_lockup_options"]' => ['value' => 'k']],
      [':input[name="su_lockup_options"]' => ['value' => 'l']],
      [':input[name="su_lockup_options"]' => ['value' => 'm']],
      [':input[name="su_lockup_options"]' => ['value' => 'n']],
      [':input[name="su_lockup_options"]' => ['value' => 'o']],
      [':input[name="su_lockup_options"]' => ['value' => 'p']],
      [':input[name="su_lockup_options"]' => ['value' => 'q']],
      [':input[name="su_lockup_options"]' => ['value' => 'r']],
      [':input[name="su_lockup_options"]' => ['value' => 's']],
    ],
  ];

  $form['su_line_4']['widget']['0']['value']['#states'] = [
    'invisible' => [
      [':input[name="su_lockup_options"]' => ['value' => 'none']],
      [':input[name="su_lockup_options"]' => ['value' => 'a']],
      [':input[name="su_lockup_options"]' => ['value' => 'b']],
      [':input[name="su_lockup_options"]' => ['value' => 'c']],
      [':input[name="su_lockup_options"]' => ['value' => 'd']],
      [':input[name="su_lockup_options"]' => ['value' => 'e']],
      [':input[name="su_lockup_options"]' => ['value' => 'f']],
      [':input[name="su_lockup_options"]' => ['value' => 'g']],
      [':input[name="su_lockup_options"]' => ['value' => 'j']],
      [':input[name="su_lockup_options"]' => ['value' => 'k']],
      [':input[name="su_lockup_options"]' => ['value' => 'l']],
      [':input[name="su_lockup_options"]' => ['value' => 'm']],
      [':input[name="su_lockup_options"]' => ['value' => 'n']],
      [':input[name="su_lockup_options"]' => ['value' => 'r']],
    ],
  ];

  $form['su_line_5']['widget']['0']['value']['#states'] = [
    'invisible' => [
      [':input[name="su_lockup_options"]' => ['value' => 'none']],
      [':input[name="su_lockup_options"]' => ['value' => 'b']],
      [':input[name="su_lockup_options"]' => ['value' => 'd']],
      [':input[name="su_lockup_options"]' => ['value' => 'e']],
      [':input[name="su_lockup_options"]' => ['value' => 'f']],
      [':input[name="su_lockup_options"]' => ['value' => 'h']],
      [':input[name="su_lockup_options"]' => ['value' => 'i']],
      [':input[name="su_lockup_options"]' => ['value' => 'l']],
      [':input[name="su_lockup_options"]' => ['value' => 'm']],
      [':input[name="su_lockup_options"]' => ['value' => 'n']],
      [':input[name="su_lockup_options"]' => ['value' => 'o']],
      [':input[name="su_lockup_options"]' => ['value' => 'p']],
      [':input[name="su_lockup_options"]' => ['value' => 'q']],
      [':input[name="su_lockup_options"]' => ['value' => 's']],
      [':input[name="su_lockup_options"]' => ['value' => 't']],
    ],
  ];

  // Clear caches on submit.
  $form['actions']["submit"]['#submit'][] = "stanford_profile_form_config_pages_lockup_settings_form_alter_submit";
}

/**
 * [stanford_profile_form_config_pages_lockup_settings_form_alter_submit description]
 * @param  array              $form       [description]
 * @param  FormStateInterface $form_state [description]
 * @return [type]                         [description]
 */
function stanford_profile_form_config_pages_lockup_settings_form_alter_submit(array &$form, FormStateInterface $form_state) {
  $renderCache = \Drupal::service('cache.render');
  $renderCache->invalidateAll();
}
