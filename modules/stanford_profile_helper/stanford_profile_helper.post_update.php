<?php

/**
 * @file
 * stanford_profile_helper.post_update.php
 */

use Drupal\Core\Url;
use Drupal\block_content\Entity\BlockContent;
use Drupal\node\NodeInterface;
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
    if (
      $pathauto_pattern &&
      $pathauto_pattern->getPattern() != $data['pattern']
    ) {
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
  $pages = \Drupal::entityTypeManager()
    ->getStorage('page')
    ->loadMultiple([
      'stanford_news_list',
      'stanford_events_upcoming',
      'people',
      'courses_list_page',
    ]);
  foreach ($pages as $page) {
    $page->delete();
  }

  $node_storage = \Drupal::entityTypeManager()->getStorage('node');
  \Drupal::service('router.builder')->rebuild();

  $pages = [
    '8ba98fcf-d390-4014-92de-c77a59b30f3b' => [
      'type' => 'stanford_event',
      'path' => '/events',
      'block' => 'f7125c85-197d-4ba2-9f6f-1126bbda0466',
    ],
    '0b83d1e9-688a-4475-9673-a4c385f26247' => [
      'type' => 'stanford_news',
      'path' => '/news',
      'block' => '5168834f-3271-4951-bd95-e75340ca5522',
    ],
    '673a8fb8-39ac-49df-94c2-ed8d04db16a7' => [
      'type' => 'stanford_person',
      'path' => '/people',
      'block' => 'fb905cf3-4bd3-4bcd-ad01-92d25e46ba32',
    ],
    '14768832-f763-4d27-8df6-7cd784886d57' => [
      'type' => 'stanford_course',
      'path' => '/courses',
      'block' => '2f343c04-f892-49bb-8d28-2c3f4653b02a',
    ],
  ];

  foreach ($pages as $uuid => $info) {
    $number_of_nodes = $node_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', $info['type'])
      ->count()
      ->execute();

    if (!$number_of_nodes) {
      \Drupal::messenger()
        ->addStatus(t('Node was not created for @path. No nodes exist for that page.', ['@path' => $info['path']]));
      continue;
    }

    $node = \Drupal::service('stanford_profile_helper.default_content')->createDefaultContent($uuid);
    if ($node) {
      _stanford_profile_helper_add_block_contents($node, $info['block']);
      _stanford_profile_helper_fix_menu_items($node->id(), $info['path']);
    }
  }
}

/**
 * Add paragraphs and rows to the node to match the given block.
 *
 * @param \Drupal\node\NodeInterface $node
 *   Node entity.
 * @param string $block_uuid
 *   Block entity UUID.
 */
function _stanford_profile_helper_add_block_contents(NodeInterface $node, $block_uuid) {
  $block_storage = \Drupal::entityTypeManager()->getStorage('block_content');
  $paragraph_storage = \Drupal::entityTypeManager()->getStorage('paragraph');
  $row_storage = \Drupal::entityTypeManager()->getStorage('paragraph_row');

  $blocks = $block_storage->loadByProperties(['uuid' => $block_uuid]);
  if (empty($blocks)) {
    return;
  }

  /** @var \Drupal\block_content\BlockContentInterface $block */
  $block = reset($blocks);
  $components = $block->get('su_component');
  if (!$components->count()) {
    return;
  }
  $rows = [];
  /** @var \Drupal\entity_reference_revisions\Plugin\Field\FieldType\EntityReferenceRevisionsItem $component */
  foreach ($components as $component) {
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $paragraph_storage->load($component->get('target_id')
      ->getString());
    $cloned_paragraph = $paragraph->createDuplicate();
    $cloned_paragraph->enforceIsNew();
    $cloned_paragraph->save();
    $row = $row_storage->create([
      'type' => 'node_stanford_page_row',
      'su_page_components' => ['entity' => $cloned_paragraph],
    ]);
    $row->save();
    $rows[] = ['entity' => $row];
  }

  $node->set('su_page_components', $rows)->save();
}

/**
 * Find menu items and change them to target the node.
 *
 * @param int $node_id
 *   Node entity id.
 * @param string $destination_path
 *   Aliased path of the node.
 */
function _stanford_profile_helper_fix_menu_items(int $node_id, string $destination_path) {
  $menu_link_storage = \Drupal::entityTypeManager()
    ->getStorage('menu_link_content');

  $menu_link_ids = $menu_link_storage->getQuery()
    ->accessCheck(FALSE)
    ->condition('link', "internal:$destination_path")
    ->execute();

  /** @var \Drupal\menu_link_content\MenuLinkContentInterface $menu_link_item */
  foreach ($menu_link_storage->loadMultiple($menu_link_ids) as $menu_link_item) {
    $link = $menu_link_item->get('link')->get(0);
    $link->set('uri', "internal:/node/$node_id");
    $menu_link_item->save();
  }
}
