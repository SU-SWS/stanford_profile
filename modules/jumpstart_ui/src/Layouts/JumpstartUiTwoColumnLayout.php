<?php

namespace Drupal\jumpstart_ui\Layouts;

use Drupal\Core\Form\FormStateInterface;

/**
 * Configurable options for the two column layout.
 *
 * @package Drupal\jumpstart_ui\Layouts
 */
class JumpstartUiTwoColumnLayout extends JumpstartUiLayouts {

  /**
   * Equal two columns.
   */
  const EQUAL = 'equal';

  /**
   * Larger left column.
   */
  const LEFT = 'left';

  /**
   * Larger right column.
   */
  const RIGHT = 'right';

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    $config = parent::defaultConfiguration();
    unset($config['columns']);
    $config['orientation'] = self::RIGHT;
    return $config;
  }

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    unset($form['columns']);
    $form['orientation'] = [
      '#type' => 'select',
      '#title' => $this->t('Orientation'),
      '#default_value' => $this->getConfiguration()['orientation'],
      '#options' => [
        self::RIGHT => $this->t('Larger Right Column'),
        self::EQUAL => $this->t('Equal Columns'),
        self::LEFT => $this->t('Larger Left Column'),
      ],
    ];
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    unset($this->configuration['columns']);
    $this->configuration['orientation'] = $form_state->getValue('orientation');
  }

}
