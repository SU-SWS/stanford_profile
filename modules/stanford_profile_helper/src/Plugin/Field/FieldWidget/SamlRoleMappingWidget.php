<?php

namespace Drupal\stanford_profile_helper\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'saml_role_mapping' widget.
 *
 * The majority of this widget was taken from stanford_ssp with some adjustments
 * to work as a field widget.
 *
 * @FieldWidget(
 *   id = "saml_role_mapping",
 *   module = "stanford_profile_helper",
 *   label = @Translation("Saml Role Mapping"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class SamlRoleMappingWidget extends WidgetBase {

  /**
   * Core entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Core config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // This widget should only be allowed on a specific field.
    return $field_definition->getName() == 'su_simplesaml_roles';
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    // If the form is newly built, the form state storage will be null. If the
    // form is being rebuilt from an ajax, the storage will be some type of
    // array.
    if (is_null($form_state->get('mappings'))) {
      $mappings = explode('|', $items->getString());
      $form_state->set('mappings', array_filter(array_combine($mappings, $mappings)));
    }

    $element['role_population'] = [
      '#type' => 'table',
      '#header' => $this->getRoleHeaders(),
      '#attributes' => ['id' => 'role-mapping-table'],
    ];

    $element['role_population']['add']['#tree'] = TRUE;
    $element['role_population']['add']['role_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Add Role'),
      '#options' => _stanford_profile_helper_get_assignable_roles(),
    ];

    $element['role_population']['add']['workgroup'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Workgroup'),
      '#description' => $this->t('The Stanford Workgroup. The workgroup must be public. eg: uit:sws'),
    ];
    $element['role_population']['add']['add_mapping'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Mapping'),
      '#submit' => [[self::class, 'addMappingCallback']],
      '#ajax' => [
        'callback' => [self::class, 'addMapping'],
        'wrapper' => 'role-mapping-table',
      ],
    ];

    foreach (($form_state->get('mappings') ?: []) as $role_mapping) {
      $element['role_population'][$role_mapping] = $this->buildRoleRow($role_mapping);
    }

    return $element;
  }

  /**
   * Add/remove a new workgroup mapping callback.
   *
   * @param array $form
   *   Complete Form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current form state.
   *
   * @return array
   *   Form element.
   */
  public static function addMapping(array &$form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $form_keys = array_slice($trigger['#parents'], 0, -4);
    // Return the entity form element.
    return NestedArray::getValue($form, $form_keys);
  }

  /**
   * Add a new workgroup mapping submit callback.
   *
   * @param array $form
   *   Complete Form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current form state.
   */
  public static function addMappingCallback(array $form, FormStateInterface $form_state) {
    $trigger = $form_state->getTriggeringElement();
    $form_state_keys = array_slice($trigger['#parents'], 0, -3);
    $form_state_value = $form_state->getValue($form_state_keys);

    $role_id = $form_state_value['role_population']['add']['role_id'];
    $workgroup = trim(Html::escape($form_state_value['role_population']['add']['workgroup']));
    // Construct the workgroup role mapping and save it to the form state.
    if ($role_id && $workgroup) {
      $mapping_string = "$role_id:eduPersonEntitlement,=,$workgroup";
      $form_state->set(['mappings', $mapping_string], $mapping_string);

      \Drupal::messenger()
        ->addWarning(t('These settings have not been saved yet.'));
    }

    $form_state->setRebuild();
  }

  /**
   * Remove a workgroup mapping submit callback.
   *
   * @param array $form
   *   Complete Form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Current form state.
   */
  public static function removeMappingCallback(array $form, FormStateInterface $form_state) {
    $mappings = $form_state->get('mappings') ?: [];
    // Remove the role mapping from the form state and rebuild the form.
    unset($mappings[$form_state->getTriggeringElement()['#mapping']]);
    $form_state->set('mappings', $mappings);
    $form_state->setRebuild();
  }

  /**
   * Get the role mapping table headers.
   *
   * @return array
   *   Array of table header labels.
   */
  protected function getRoleHeaders() {
    return [
      $this->t('Role'),
      $this->t('Workgroup'),
      $this->t('Actions'),
    ];
  }

  /**
   * Build the table row for the role mapping string.
   *
   * @param string $role_mapping_string
   *   Formatted role mapping string.
   *
   * @return array
   *   Table render array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function buildRoleRow($role_mapping_string) {
    [$role_id, $comparison] = explode(':', $role_mapping_string, 2);

    $exploded_comparison = explode(',', $comparison, 3);

    $value = end($exploded_comparison);
    $role = $this->entityTypeManager->getStorage('user_role')
      ->load($role_id);

    return [
      ['#markup' => $role ? $role->label() : $this->t('Broken: @id', ['@id' => $role_id])],
      ['#markup' => $value],
      [
        '#type' => 'submit',
        '#value' => $this->t('Remove Mapping'),
        '#name' => $role_mapping_string,
        '#submit' => [[self::class, 'removeMappingCallback']],
        '#mapping' => $role_mapping_string,
        '#ajax' => [
          'callback' => [self::class, 'addMapping'],
          'wrapper' => 'role-mapping-table',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $attribute = $this->configFactory->get('stanford_ssp.settings')
      ->get('saml_attribute') ?: 'eduPersonEntitlement';
    $mappings = ['administrator:eduPersonEntitlement,=,uit:sws'];
    foreach ($values[0]['role_population'] as $key => $value) {
      if ($key == 'add') {
        if (!empty($value['workgroup'])) {
          $mappings[] = "{$value['role_id']}:$attribute,=,{$value['workgroup']}";
        }
        continue;
      }
      $mappings[] = $key;
    }

    $values = [implode('|', array_unique($mappings))];
    return parent::massageFormValues($values, $form, $form_state);
  }

}
