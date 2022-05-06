<?php

/**
 * @file
 * stanford_profile_helper.post_update.php
 */

use Drupal\Core\Serialization\Yaml;
use Drupal\Core\Url;
use Drupal\block_content\Entity\BlockContent;
use Drupal\user\Entity\Role;

/**
 * Create the events and news intro block content.
 */
function stanford_profile_helper_post_update_8000(&$sandbox) {
  BlockContent::create([
    'uuid' => 'f7125c85-197d-4ba2-9f6f-1126bbda0466',
    'type' => 'stanford_component_block',
    'info' => 'Events Intro',
  ])->save();
  BlockContent::create([
    'uuid' => '5168834f-3271-4951-bd95-e75340ca5522',
    'type' => 'stanford_component_block',
    'info' => 'News Intro',
  ])->save();
}

/**
 * Clear out the state that limits the paragraph types.
 */
function stanford_profile_helper_post_update_8001() {
  \Drupal::state()->delete('stanford_profile_allow_all_paragraphs');
}

/**
 * Set the link title for media caption paragraph link fields.
 */
function stanford_profile_helper_post_update_8100() {
  $paragraph_storage = \Drupal::entityTypeManager()
    ->getStorage('paragraph');
  $entity_ids = $paragraph_storage->getQuery()
    ->accessCheck(FALSE)
    ->condition('type', 'stanford_media_caption')
    ->exists('su_media_caption_link')
    ->execute();

  foreach ($paragraph_storage->loadMultiple($entity_ids) as $paragraph) {
    $value = $paragraph->get('su_media_caption_link')->get(0)->getValue();
    $link_url = $value['uri'];

    $title = NULL;
    try {
      $url = Url::fromUri($link_url);
      if ($url->isRouted()) {
        // The only routed urls the link field supports is to nodes.
        $parameters = $url->getRouteParameters();
        $node = \Drupal::entityTypeManager()
          ->getStorage('node')
          ->load($parameters['node']);
        $title = $node ? $node->label() : NULL;
      }

      // Absolute external url, fetch the contents of the page and grab the
      // `<title>` value.
      if ($url->isExternal()) {
        $page = file_get_contents($url->toString());
        $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $page, $match) ? $match[1] : NULL;
      }

      // If no title is found above, use the last part of the url as a sensible
      // default to try an establish an human readable title.
      if (!$title) {
        $title = substr($url->toString(), strrpos($url->toString(), '/') + 1);
        $title = ucwords(preg_replace('/[^\da-z]/i', ' ', $title));

        // If STILL no title, throw an error to trigger the logger in the catch.
        if (!$title) {
          throw new \Exception('Trigger log');
        }
      }
    }
    catch (\Exception $e) {
      \Drupal::logger('stanford_profile_helper')
        ->error('Unable to set link title for paragraph %id with url %url', [
          '%id' => $paragraph->id(),
          '%url' => $link_url,
        ]);
      continue;
    }

    if ($title) {
      $value['title'] = trim($title);
      $paragraph->set('su_media_caption_link', [$value])->save();
    }
  }
}

/**
 * Add layout builder user role if content has custom LB settings.
 */
function stanford_profile_helper_post_update_8101() {
  // Create the new role. It'll get updated via config import later.
  Role::create(['id' => 'layout_builder_user', 'label' => 'LB User'])->save();
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');

  // Find all nodes that have a custom layout builder layout.
  $nids = $node_storage->getQuery()
    ->accessCheck(FALSE)
    ->condition('layout_builder__layout', NULL, 'IS NOT NULL')
    ->execute();

  $user_ids = [];

  // Gather all of the user ids that have created or last edited the node.
  foreach ($nids as $nid) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $node_storage->load($nid);
    $user_ids[] = (int) $node->get('uid')->getString();
    $user_ids[] = (int) $node->get('revision_uid')->getString();
  }
  $users = \Drupal::entityTypeManager()
    ->getStorage('user')
    ->loadMultiple(array_unique($user_ids));

  // If the users have a site manager role, add the layout builder role.
  /** @var \Drupal\user\UserInterface $user */
  foreach ($users as $user) {
    if ($user->hasRole('site_manager')) {
      $user->addRole('layout_builder_user');
      $user->save();
    }
  }
}

/**
 * Modify path auto patterns.
 */
function stanford_profile_helper_post_update_8102() {
  /** @var \Drupal\Core\Config\StorageInterface $config_storage */
  $config_storage = \Drupal::service('config.storage.sync');
  $pathauto_configs = $config_storage->listAll('pathauto.pattern.');
  $configs = $config_storage->readMultiple($pathauto_configs);

  $pathauto_storage = \Drupal::entityTypeManager()
    ->getStorage('pathauto_pattern');
  foreach ($configs as $data) {
    /** @var \Drupal\pathauto\PathautoPatternInterface $pathauto_pattern */
    $pathauto_pattern = $pathauto_storage->load($data['id']);
    if ($pathauto_pattern->getPattern() != $data['pattern']) {
      $pathauto_pattern->setPattern($data['pattern'])->save();
    }
  }

  $terms = \Drupal::entityTypeManager()
    ->getStorage('taxonomy_term')
    ->loadByProperties([
      'vid' => [
        'stanford_event_types',
        'stanford_news_topics',
        'stanford_publication_topics',
      ],
    ]);
  foreach ($terms as $term) {
    $term->save();
  }
}

/**
 * Re-save all event, publications, and people nodes and terms.
 */
function stanford_profile_helper_post_update_8103(&$sandbox) {
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  if (empty($sandbox['ids'])) {
    $sandbox['ids'] = $node_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', [
        'stanford_event',
        'stanford_person',
        'stanford_publication',
      ], 'IN')
      ->execute();
    $sandbox['total'] = count($sandbox['ids']);
  }
  $node_ids = array_splice($sandbox['ids'], 0, 10);

  /** @var \Drupal\node\NodeInterface $node */
  foreach ($node_storage->loadMultiple($node_ids) as $node) {
    $node->save();
  }

  $sandbox['#finished'] = count($sandbox['ids']) ? 1 - count($sandbox['ids']) / $sandbox['total'] : 1;
}

/**
 * Create event, news, & people landing nodes.
 */
function stanford_profile_helper_post_update_9000() {
  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  $alias_storage = \Drupal::entityTypeManager()->getStorage('path_alias');
  $current_profile = \Drupal::config('core.extension')->get('profile');;
  $profile_path = \Drupal::service('extension.list.profile')
    ->getPath($current_profile);
  /** @var \Drupal\default_content\Normalizer\ContentEntityNormalizer $normalizer */
  $normalizer = \Drupal::service('default_content.content_entity_normalizer');
  $pages = [
    '8ba98fcf-d390-4014-92de-c77a59b30f3b' => [
      'path' => '/events',
      'type' => 'stanford_events',
    ],
    '0b83d1e9-688a-4475-9673-a4c385f26247' => [
      'path' => '/news',
      'type' => 'stanford_news',
    ],
    '673a8fb8-39ac-49df-94c2-ed8d04db16a7' => [
      'path' => '/people',
      'type' => 'stanford_person',
    ],
  ];

  foreach ($pages as $uuid => $info) {
    $number_of_nodes = $node_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', $info['type'])
      ->count()
      ->execute();

    if ($alias_storage->loadByProperties(['alias' => $info['path']]) || !$number_of_nodes) {
      \Drupal::messenger()
        ->addStatus(t('Node was not created. Either @path exists or no nodes exist for that page.', ['@path' => $info['path']]));
      continue;
    }

    $file_path = "$profile_path/content/node/$uuid.yml";
    if (file_exists($file_path)) {
      $decoded = Yaml::decode(file_get_contents($file_path));
      $entity = $normalizer->denormalize($decoded);
      $entity->save();
    }
  }
}
