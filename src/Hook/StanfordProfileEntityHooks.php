<?php

declare(strict_types=1);

namespace Drupal\stanford_profile\Hook;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\stanford_profile\EventSubscriber\StanfordProfileEventSubscriber;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;

class StanfordProfileEntityHooks {

  #[Hook('entity_insert')]
  #[Hook('entity_delete')]
  public function entityInsert(EntityInterface $entity) {
    if ($entity->getEntityTypeId() == 'user_role') {
      self::updateSamlauthRoles();
    }
  }

  #[Hook('entity_presave')]
  public function entityPreSave(EntityInterface $entity) {
    // Invalidate the site renewal redirect logic in case the user now has
    // permissions to make the needed changes.
    if ($entity->getEntityTypeId() == 'user') {
      Cache::invalidateTags(['site-renew-date']);
    }

    if (
      PHP_SAPI != 'cli' &&
      $entity->getEntityTypeId() == 'config_pages' &&
      $entity->bundle() == 'stanford_basic_site_settings' &&
      StanfordProfileEventSubscriber::redirectUser()
    ) {
      $renewal_date = time() + 60 * 60 * 24 * 365;
      $entity->set('su_site_renewal_due', date(DateTimeItemInterface::DATETIME_STORAGE_FORMAT, $renewal_date));
      Cache::invalidateTags(['site-renew-date']);
    }
  }

  /**
   * Update samlauth allowed roles settings.
   */
  protected static function updateSamlauthRoles() {
    if (!\Drupal::moduleHandler()->moduleExists('samlauth')) {
      return;
    }

    $role_ids = array_keys(Role::loadMultiple());
    $role_ids = array_combine($role_ids, $role_ids);
    unset($role_ids[RoleInterface::AUTHENTICATED_ID]);
    asort($role_ids);

    $config = \Drupal::configFactory()->getEditable('samlauth.authentication');
    $config->set('map_users_roles', $role_ids)->save();
  }

}
