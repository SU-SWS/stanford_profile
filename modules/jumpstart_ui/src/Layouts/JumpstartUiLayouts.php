<?php

namespace Drupal\jumpstart_ui\Layouts;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\PluginFormInterface;

/**
 * Class JumpstartUiLayouts.
 *
 * @package Drupal\jumpstart_ui
 */
class JumpstartUiLayouts extends LayoutDefault implements PluginFormInterface {

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    return ['extra_classes' => NULL, 'centered' => TRUE, 'columns' => 'default'];
  }

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    // Extra CSS classes.
    $form['extra_classes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Extra Classes'),
      '#description' => $this->t('Add extra classes to the layout container.'),
      '#default_value' => $this->configuration['extra_classes'],
    ];

    // Centered container.
    $form['centered'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Centered Container'),
      '#default_value' => (bool) $this->configuration['centered'],
    ];

    // Columns.
    $form['columns'] = [
      '#type' => 'select',
      '#title' => $this->t('Maximum Width'),
      '#description' => $this->t('Set the maximum width of the container.'),
      '#options' => [
        'default' => $this->t('Default'),
        'flex-3-of-12' => $this->t('3 Columns'),
        'flex-4-of-12' => $this->t('4 Columns'),
        'flex-5-of-12' => $this->t('5 Columns'),
        'flex-6-of-12' => $this->t('6 Columns'),
        'flex-7-of-12' => $this->t('7 Columns'),
        'flex-8-of-12' => $this->t('8 Columns'),
        'flex-9-of-12' => $this->t('9 Columns'),
        'flex-10-of-12' => $this->t('10 Columns'),
        'flex-11-of-12' => $this->t('11 Columns'),
        'flex-12-of-12' => $this->t('12 Columns'),
      ],
      '#default_value' => $this->configuration['columns'] ?? 'default',
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $classes = explode(' ', $form_state->getValue('extra_classes'));
    $classes = array_map([
      '\Drupal\Component\Utility\Html',
      'cleanCssIdentifier',
    ], $classes);
    array_walk($classes, 'trim');
    $this->configuration['extra_classes'] = implode(' ', array_filter($classes));
    $this->configuration['centered'] = $form_state->getValue('centered') ? 'centered-container' : NULL;
    $this->configuration['columns'] = $form_state->getValue('columns');
  }

}
