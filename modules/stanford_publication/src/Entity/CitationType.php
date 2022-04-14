<?php

namespace Drupal\stanford_publication\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Citation type entity.
 *
 * @ConfigEntityType(
 *   id = "citation_type",
 *   label = @Translation("Citation type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\stanford_publication\CitationTypeListBuilder",
 *     "access" = "Drupal\stanford_publication\CitationTypeAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\stanford_publication\Form\CitationTypeForm",
 *       "edit" = "Drupal\stanford_publication\Form\CitationTypeForm",
 *       "delete" = "Drupal\stanford_publication\Form\CitationTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "citation_type",
 *   admin_permission = "administer site configuration",
 *   bundle_of = "citation",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/citation_type/{citation_type}",
 *     "add-form" = "/admin/structure/citation_type/add",
 *     "edit-form" = "/admin/structure/citation_type/{citation_type}/edit",
 *     "delete-form" = "/admin/structure/citation_type/{citation_type}/delete",
 *     "collection" = "/admin/structure/citation_type"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "type"
 *   }
 * )
 */
class CitationType extends ConfigEntityBundleBase implements CitationTypeInterface {

  /**
   * The Citation type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Citation type label.
   *
   * @var string
   */
  protected $label;

  /**
   * The citation/bibliography type.
   *
   * @var string
   */
  protected $type;

  /**
   * {@inheritDoc}
   */
  public function type(): ?string {
    return $this->type;
  }

}
