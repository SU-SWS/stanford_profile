<?php

namespace Drupal\stanford_profile_helper;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 *
 */
interface StanfordDefaultContentInterface {

  public function createDefaultListPage($page_uuid): ?ContentEntityInterface;

}
