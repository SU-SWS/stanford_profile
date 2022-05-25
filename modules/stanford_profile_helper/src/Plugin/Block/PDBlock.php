<?php

namespace Drupal\stanford_profile_helper\Plugin\Block;

use Drupal\pdb\Plugin\Block\PdbBlock;

/**
 * Exposes a React component as a block.
 *
 * @Block(
 *   id = "pdb_component",
 *   admin_label = @Translation("PDB component"),
 *   deriver = "\Drupal\stanford_profile_helper\Plugin\Derivative\ReactBlockDeriver"
 * )
 */
class PDBlock extends PdbBlock {

  /**
   * {@inheritDoc}
   */
  public function attachLibraries(array $component) {
    return ['library' => parent::attachLibraries($component)];
  }

}
