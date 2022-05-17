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
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   *   Constructed node entity.
   */
  public function createDefaultListPage(string $page_uuid): ?ContentEntityInterface;

}
