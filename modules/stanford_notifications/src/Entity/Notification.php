<?php

namespace Drupal\stanford_notifications\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\stanford_notifications\NotificationInterface;

/**
 * Defines the notification entity class.
 *
 * @ContentEntityType(
 *   id = "notification",
 *   label = @Translation("Notification"),
 *   base_table = "notification",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid"
 *   }
 * )
 */
class Notification extends ContentEntityBase implements NotificationInterface {

  /**
   * {@inheritDoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    $fields['message'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Message'))
      ->setSetting('max_length', 500)
      ->setRequired(TRUE)
      ->setReadOnly(TRUE);
    $fields['status'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Status'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE);
    $fields['uid'] = BaseFieldDefinition::create('integer')
      ->setLabel(new TranslatableMarkup('User ID'))
      ->setRequired(TRUE)
      ->setReadOnly(TRUE);
    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function message() {

    return $this->get('message')->getString();
  }

  /**
   * {@inheritDoc}
   */
  public function userId() {
    return (int) $this->get('uid')->getString();
  }

  /**
   * {@inheritDoc}
   */
  public function delete() {
    parent::delete();
    Cache::invalidateTags(['notification:' . $this->id()]);
  }

  /**
   * {@inheritDoc}
   */
  public function status() {
    return $this->get('status')->getString();
  }

}
