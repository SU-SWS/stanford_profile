<?php

namespace Drupal\jumpstart_ui\Layouts;

use Drupal\Core\Form\FormStateInterface;

/**
 * Configurable options for the three column layout.
 *
 * @package Drupal\jumpstart_ui\Layouts
 */
class JumpstartUiThreeColumnLayout extends JumpstartUiLayouts {

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    $config = parent::defaultConfiguration();
    unset($config['columns']);
    return $config;
  }

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    unset($form['columns']);
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    unset($this->configuration['columns']);
  }

}
