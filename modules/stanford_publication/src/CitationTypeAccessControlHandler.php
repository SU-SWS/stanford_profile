<?php

namespace Drupal\stanford_publication;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Citation type entity.
 *
 * @see \Drupal\stanford_publication\Entity\Citation.
 */
class CitationTypeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // Allow access to view the citation type so that anonymous users can view
    // the bundle label. The entity type never has anything of sensitivity and
    // is only 3 values anyways. This allows us to display the bundle in the
    // views and on the node page containing the citation.
    if ($operation != 'view') {
      return parent::checkAccess($entity, $operation, $account);
    }
    return AccessResult::allowed();
  }

}
