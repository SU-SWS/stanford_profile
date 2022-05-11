<?php

namespace Drupal\stanford_intranet;

/**
 * Intranet manager service interface.
 */
interface StanfordIntranetManagerInterface {

  /**
   * Move public files into the private file system.
   */
  public function moveIntranetFiles(): void;

}
