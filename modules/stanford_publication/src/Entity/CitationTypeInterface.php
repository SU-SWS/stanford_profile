<?php

namespace Drupal\stanford_publication\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface CitationTypeInterface.
 *
 * @package Drupal\stanford_publication\Entity
 */
interface CitationTypeInterface extends ConfigEntityInterface {

  /**
   * Get the citation/bibliography type.
   *
   * @link https://docs.citationstyles.org/en/1.0.1/specification.html#appendix-iii-types
   *
   * @return string|null
   *   The CSL type.
   */
  public function type(): ?string;

}
