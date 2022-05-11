<?php

namespace Drupal\stanford_publication;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Citation entity.
 *
 * @see \Drupal\stanford_publication\Entity\Citation.
 */
class CitationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\stanford_publication\Entity\CitationInterface $entity */

    switch ($operation) {

      case 'view':
        return AccessResult::allowed();

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit citation entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete citation entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $admin_permission = $this->entityType->getAdminPermission();
    $permissions = [$admin_permission, 'add citation entities'];
    // If the user has admin permission or permission to add new entities.
    return AccessResult::allowedIfHasPermissions($account, $permissions, 'OR');
  }

}
