<?php

/**
 * @file
 * cardinal_service_profile.inc
 */

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\field\FieldConfigInterface;
use Drupal\node\NodeInterface;
use Drupal\views\ViewExecutable;

/**
 * Implements hook_ENTITY_TYPE_presave().
 */
function cardinal_service_profile_node_presave(NodeInterface $node) {
  if ($node->bundle() == 'su_spotlight' && $node->get('su_spotlight_student_name')
      ->getString() === $node->label()) {
    $node->set('su_spotlight_student_name', NULL);
    return;
  }

  if ($node->bundle() != 'su_opportunity' || !$node->hasField('su_opp_application_deadline')) {
    return;
  }

  $deadline_date = $node->get('su_opp_application_deadline')->getString();
  if (empty($deadline_date)) {
    return;
  }

  // Compare the month and day of the deadline field value to taxonomy terms to
  // find out what term has the applicable date range.
  $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')
    ->loadByProperties(['vid' => 'su_opportunity_deadline']);

  /** @var \Drupal\taxonomy\TermInterface $term */
  foreach ($terms as $term) {
    $deadline_range = $term->get('su_opp_deadline_date')->getValue();
    // The term doesn't have the field value set, skip it.
    if (empty($deadline_range[0]['value'])) {
      continue;
    }

    // This term is configured for the range surrounding the deadline date field
    // so we use that term id as the deadline term.
    if (_cardinal_service_profile_between_dates($deadline_range[0]['value'], $deadline_range[0]['end_value'], $deadline_date)) {
      $node->set('su_opp_deadline_time', $term->id());
      return;
    }

  }

}

/**
 * Check if a given date is between two other dates in terms of month and day.
 *
 * Compare only the month and day of the string. We don't need to check anything
 * with the year, hours, or minutes.
 *
 * @param string $from_date
 *   Date string in the form of 2020-01-30.
 * @param string $to_date
 *   Date string in the form of 2020-01-30.
 * @param string $date
 *   Date string in the form of 2020-01-30T12:35:11.
 *
 * @return bool
 *   True if the date is between.
 */
function _cardinal_service_profile_between_dates($from_date, $to_date, $date) {
  $date = (int) date('md', strtotime($date));

  list(, $from_month, $from_day) = explode('-', $from_date);
  list(, $to_month, $to_day) = explode('-', $to_date);

  $from = (int) "$from_month$from_day";
  $to = (int) "$to_month$to_day";

  if ($from > $to) {
    return $date >= $from || $date <= $to;
  }
  return $date >= $from && $date <= $to;
}

/**
 * Implements hook_preprocess_hook().
 */
function cardinal_service_profile_preprocess_flag(&$variables) {
  if (\Drupal::currentUser()->isAuthenticated()) {
    return;
  }

  unset($variables['attributes']['class']);
  $current_url = \Drupal::request()->getRequestUri();
  $separator = strpos($current_url, '?') !== FALSE ? '&' : '?';
  $current_url .= $separator . 'flag_type=' . $variables['flaggable']->getEntityTypeId() . '&flag_id=' . $variables['flaggable']->id() . '&flag=' . $variables['flag']->id();
  $current_url = urlencode($current_url);
  $variables['attributes']['href'] = "/saml_login?destination=$current_url";
}

/**
 * Implements hook_user_login().
 */
function cardinal_service_profile_user_login($account) {
  $request_queries = \Drupal::request()->query;
  // User logged in without a destination parameter, nothing to do.
  if (!$request_queries->has('destination')) {
    return;
  }

  // The destination exists, but it doesn't contain any query parameters that
  // might be flag params, nothing to do.
  $url = parse_url($request_queries->get('destination'));
  if (empty($url['query'])) {
    return;
  }

  // The destination query doesn't contain all the appropriate parameters,
  // nothing to do.
  parse_str($url['query'], $params);
  if (!(isset($params['flag_type']) && isset($params['flag_id']) && isset($params['flag']))) {
    return;
  }

  $flag = [
    'flag_id' => $params['flag'],
    'entity_type' => $params['flag_type'],
    'entity_id' => $params['flag_id'],
    'uid' => $account->id(),
  ];

  // Look for an existing flag for the current user. If a flag entity exists,
  // the user has already saved it. If the flag entity doesn't exist, create it
  // so it's saved for the user.
  $flagging_storage = \Drupal::entityTypeManager()->getStorage('flagging');
  if (empty($flagging_storage->loadByProperties($flag))) {
    $flagging_storage->create($flag)->save();
  }
}

/**
 * Implements hook_views_pre_build().
 */
function cardinal_service_profile_views_pre_build(ViewExecutable $view) {
  // On the user dashboard, add a contextual argument to the news view to
  // display the announcement news items.
  if (
    $view->id() == 'stanford_news' &&
    \Drupal::routeMatch()->getRouteName() == 'entity.user.canonical'
  ) {
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties(['name' => 'Announcements']);
    $view->setArguments(array_keys($terms));
    $view->empty = [];
  }
}

/**
 * Implements hook_entity_form_display_alter().
 */
function cardinal_service_profile_entity_form_display_alter(EntityFormDisplayInterface &$form_display, array $context) {
  $route_name = \Drupal::routeMatch()->getRouteName();
  // Make sure we are editing the form only for editing users.
  if (
    \Drupal::currentUser()->hasPermission('administer account settings') ||
    !isset($context['entity_type']) ||
    $context['entity_type'] !== 'user' ||
    $route_name !== 'entity.user.edit_form'
  ) {
    return;
  }

  /** @var \Drupal\Core\Entity\EntityDisplayRepository $entity_display_repo */
  $entity_display_repo = \Drupal::service('entity_display.repository');
  $form_display = $entity_display_repo->getFormDisplay($context['entity_type'], $context['bundle'], 'authenticated_edit') ?: $form_display;
}

/**
 * Implements hook_ui_patterns_info_alter().
 */
function cardinal_service_profile_ui_patterns_info_alter(array &$definitions) {
  if (isset($definitions['yaml:quote'])) {
    $definitions['yaml:quote']->setField('content', 'Additional Content');
    $definitions['yaml:quote']->setField('link', 'Link to More Content');
  }
}

/**
 * Implements hook_react_paragraphs_form_field_data_alter().
 */
function cardinal_service_profile_react_paragraphs_form_field_data_alter(array &$info, array $field_element, FieldConfigInterface $field_config) {
  if ($field_config->getName() == 'su_opp_list_view') {
    $good_displays = ['context_filtered_opps'];
    foreach ($info['displays']['su_opportunities'] as $key => $display) {
      if (!in_array($display['value'], $good_displays)) {
        unset($info['displays']['su_opportunities'][$key]);
      }
    }
    $info['displays']['su_opportunities'] = array_values($info['displays']['su_opportunities']);
  }
}

/**
 * Implements hook_form_alter().
 */
function cardinal_service_profile_form_alter(&$form, FormStateInterface $form_state, $form_id) {
  if ($form_id == 'node_su_opportunity_edit_form') {
    /** @var \Drupal\node\NodeForm $form_object */
    $form_object = $form_state->getBuildInfo()['callback_object'];
    $entity = $form_object->getEntity();

    // The current node was imported via migration, present the user with a
    // message and a link to that profile.
    if (_cardinal_service_node_imported($entity)) {
      \Drupal::messenger()
        ->addWarning(t('Some fields can not be edited since they contain imported data. They are not visible here.'));
    }
  }
}

/**
 * Implements hook_entity_field_access().
 */
function cardinal_service_profile_entity_field_access($operation, FieldDefinitionInterface $field_definition, AccountInterface $account, FieldItemListInterface $items = NULL) {
  $route_match = \Drupal::routeMatch();
  // When edit an existing node that was imported via migrate module, mark the
  // fields that are mapped from migration as forbidden.
  if (
    $operation == 'edit' &&
    $route_match->getRouteName() == 'entity.node.edit_form'
  ) {

    $node = $route_match->getParameter('node');
    if (_cardinal_service_profile_field_mapped($node, $field_definition)) {
      return AccessResult::forbidden();
    }
  }
  return AccessResult::neutral();
}

/**
 * Check if the field is mapped by a migration and the node was imported.
 *
 * @param \Drupal\node\NodeInterface $node
 *   Node entity being edited.
 * @param \Drupal\Core\Field\FieldDefinitionInterface $field
 *   Field to check for mapping.
 *
 * @return bool
 *   If the node is imported and the field is mapped.
 */
function _cardinal_service_profile_field_mapped(NodeInterface $node, FieldDefinitionInterface $field) {
  $field_name = $field->getName();
  $columns = $field->getFieldStorageDefinition()->getColumns();

  if ($migration_name = _cardinal_service_node_imported($node)) {
    $migration_config = \Drupal::config("migrate_plus.migration.$migration_name");
    $migration_group = \Drupal::config('migrate_plus.migration_group.' . $migration_config->get('migration_group'));

    $processing = FALSE;
    foreach (array_keys($columns) as $column) {
      $processing = !$processing ? $migration_config->get("process.$field_name/$column") : TRUE;
      $processing = $processing ?: $migration_group->get("shared_configuration.process.$field_name/$column");
    }

    // The field or a column of the field was mapped with data from migrate.
    // Mark it as forbidden.
    if (
      $processing ||
      $migration_config->get("process.$field_name") ||
      $migration_group->get("shared_configuration.process.$field_name")
    ) {
      return TRUE;
    }
  }

  return FALSE;
}

/**
 * Was this node imported via migrate module.
 *
 * @param \Drupal\node\NodeInterface $node
 *   Node entity.
 *
 * @return string|null
 *   True if it was imported from migration.
 */
function _cardinal_service_node_imported(NodeInterface $node) {
  $migration_names = \Drupal::configFactory()
    ->listAll('migrate_plus.migration.');

  foreach ($migration_names as $config_name) {
    $migration_name = str_replace('migrate_plus.migration.', '', $config_name);
    $db_name = 'migrate_map_' . $migration_name;
    $database = \Drupal::database();
    // Check for the table first.
    if (
      $database->schema()->tableExists($db_name) &&
      $database->schema()->fieldExists($db_name, 'destid1')
    ) {
      // Check for the entity id in the destination id column.
      $migrated = $database->select($db_name, 'm')
        ->fields('m')
        ->condition('destid1', $node->id())
        ->countQuery()
        ->execute()
        ->fetchField();

      if ($migrated > 0) {
        return $migration_name;
      }
    }
  }
}

/**
 * Implements hook_entity_bundle_field_info_alter().
 */
function cardinal_service_profile_entity_bundle_field_info_alter(&$fields, EntityTypeInterface $entity_type, $bundle) {
  if ($bundle === 'stanford_page') {
    $resource_fields = [
      'su_page_resource_type',
      'su_page_resource_audience',
      'su_page_resource_dimension',
    ];
    foreach ($resource_fields as $resource_field) {
      if (isset($fields[$resource_field])) {
        $fields[$resource_field]->addConstraint('ResourceFields', []);
      }
    }
  }
}
