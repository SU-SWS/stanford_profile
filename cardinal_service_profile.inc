<?php

/**
 * @file
 * cardinal_service_profile.inc
 */

use Drupal\node\NodeInterface;
use Drupal\views\ViewExecutable;
use Drupal\Core\Entity\Display\EntityFormDisplayInterface;

/**
 * Implements hook_ENTITY_TYPE_presave().
 */
function cardinal_service_profile_node_presave(NodeInterface $node) {
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
  }
}
