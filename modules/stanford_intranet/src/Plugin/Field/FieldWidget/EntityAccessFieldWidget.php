<?php

namespace Drupal\stanford_intranet\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'entity_access' widget.
 *
 * @FieldWidget(
 *   id = "entity_access",
 *   module = "stanford_intranet",
 *   label = @Translation("Entity Access"),
 *   field_types = {
 *     "entity_access"
 *   },
 *   multiple_values = TRUE
 * )
 */
class EntityAccessFieldWidget extends WidgetBase {

  /**
   * State servivce.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('state')
    );
  }

  /**
   * {@inheritDoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, StateInterface $state) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    if (!$this->state->get('stanford_intranet', FALSE)) {
      return $element;
    }

    $default_value = [];
    foreach ($items as $item) {
      $default_value[] = $item->getValue()['role'];
    }
    if (empty($options = _stanford_profile_helper_get_assignable_roles())) {
      return $element;
    }

    $element += [
      '#type' => 'checkboxes',
      '#options' => $options,
      '#default_value' => $default_value ?: ['authenticated'],
    ];
    return $element;
  }

  /**
   * {@inheritDoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $new_values = [];
    foreach (array_filter($values) as $role) {
      $new_values[] = ['role' => $role, 'access' => ['view']];
    }
    return $new_values;
  }

}
