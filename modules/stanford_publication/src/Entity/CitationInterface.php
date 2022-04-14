<?php

namespace Drupal\stanford_publication\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Citation entities.
 *
 * @ingroup stanford_publication
 */
interface CitationInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * APA Bibliography style.
   */
  const APA = 'apa';

  /**
   * Chicago bibliography style.
   */
  const CHICAGO = 'chicago-fullnote-bibliography';

  /**
   * Sets the Citation name.
   *
   * @param string $title
   *   The Citation title.
   *
   * @return \Drupal\stanford_publication\Entity\CitationInterface
   *   The called Citation entity.
   */
  public function setLabel($title): CitationInterface;

  /**
   * Get the bibliography html for the entity.
   *
   * @param string $style
   *   Bibliography style.
   *
   * @return string
   *   Generated HTML string.
   */
  public function getBibliography($style = self::APA): string;

  /**
   * Gets the parent entity of the paragraph.
   *
   * Preserves language context with translated entities.
   *
   * @return \Drupal\Core\Entity\ContentEntityInterface|null
   *   The parent entity.
   */
  public function getParentEntity();

  /**
   * Set the parent entity of the paragraph.
   *
   * @param \Drupal\Core\Entity\ContentEntityInterface $parent
   *   The parent entity.
   * @param string $parent_field_name
   *   The parent field name.
   *
   * @return $this
   */
  public function setParentEntity(ContentEntityInterface $parent, $parent_field_name);

}
