<?php

use Drupal\Core\Form\FormStateInterface;

// @codeCoverageIgnoreStart

// Set theme name to use in the key values.
$theme_name = \Drupal::theme()->getActiveTheme()->getName();

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function minimally_branded_subtheme_form_system_theme_settings_alter(array &$form, FormStateInterface $form_state) {

  $form['variant_settings'] = [
    '#type' => 'fieldset',
    '#title' => t('Theme Variant Settings'),
    '#description' => t('Customize the visual appearance of your site with different style variants.'),
  ];

  // Color scheme variant.
  $form['variant_settings']['minimally_branded_subtheme_color_scheme'] = [
    '#type' => 'fieldset',
    '#title' => t('Color Scheme Variant'),
  ];

  $form['variant_settings']['minimally_branded_subtheme_color_scheme']['color_scheme_variant'] = [
    '#type' => 'select',
    '#title' => t('Color Scheme'),
    '#options' => [
      'default' => t('- Default -'),
      'light' => t('Light'),
      'dark' => t('Dark'),
      'minimal' => t('Minimal'),
    ],
    '#default_value' => theme_get_setting('color_scheme_variant'),
    '#description' => t('Choose a color scheme variant for your site.'),
  ];

  // Layout variant.
  $form['variant_settings']['minimally_branded_subtheme_layout'] = [
    '#type' => 'fieldset',
    '#title' => t('Layout Variant'),
  ];

  $form['variant_settings']['minimally_branded_subtheme_layout']['layout_variant'] = [
    '#type' => 'select',
    '#title' => t('Layout Style'),
    '#options' => [
      'default' => t('- Default -'),
      'centered' => t('Centered'),
      'wide' => t('Wide'),
      'compact' => t('Compact'),
    ],
    '#default_value' => theme_get_setting('layout_variant'),
    '#description' => t('Choose a layout variant for your site content.'),
  ];

  // Typography variant.
  $form['variant_settings']['minimally_branded_subtheme_typography'] = [
    '#type' => 'fieldset',
    '#title' => t('Typography Variant'),
  ];

  $form['variant_settings']['minimally_branded_subtheme_typography']['typography_variant'] = [
    '#type' => 'select',
    '#title' => t('Typography Style'),
    '#options' => [
      'default' => t('- Default -'),
      'serif' => t('Serif'),
      'sans-serif' => t('Sans-serif'),
      'mixed' => t('Mixed'),
    ],
    '#default_value' => theme_get_setting('typography_variant'),
    '#description' => t('Choose a typography variant for your site.'),
  ];

  // Button style variant.
  $form['variant_settings']['minimally_branded_subtheme_buttons'] = [
    '#type' => 'fieldset',
    '#title' => t('Button Style Variant'),
  ];

  $form['variant_settings']['minimally_branded_subtheme_buttons']['button_variant'] = [
    '#type' => 'select',
    '#title' => t('Button Style'),
    '#options' => [
      'default' => t('- Default -'),
      'rounded' => t('Rounded'),
      'square' => t('Square'),
      'minimal' => t('Minimal'),
    ],
    '#default_value' => theme_get_setting('button_variant'),
    '#description' => t('Choose a button style variant for your site.'),
  ];
}

// @codeCoverageIgnoreEnd