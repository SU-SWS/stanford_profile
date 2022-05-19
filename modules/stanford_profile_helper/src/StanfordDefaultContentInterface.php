<?php

namespace Drupal\stanford_profile_helper;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Default content service interface.
 */
interface StanfordDefaultContentInterface {

  /**
   * Use the default_content module to create a node with the given uuid.
   *
   * @param string $page_uuid
   *   Node UUID and filename.
   * @param string $type
   *   Module, profile, or theme type that hosts the default content.
   * @param string $name
   *   Extension name.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   *   Constructed node entity.
   */
  public function createDefaultContent(string $page_uuid, string $type = 'profile', string $name = ''): ?ContentEntityInterface;

}
