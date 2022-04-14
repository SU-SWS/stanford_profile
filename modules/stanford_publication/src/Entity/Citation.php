<?php

namespace Drupal\stanford_publication\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Link;
use Drupal\Core\TypedData\TranslatableInterface;
use Drupal\Core\Url;
use Drupal\field\Entity\FieldStorageConfig;
use Seboettg\CiteProc\CiteProc;

/**
 * Defines the Citation entity.
 *
 * @ingroup stanford_publication
 *
 * @ContentEntityType(
 *   id = "citation",
 *   label = @Translation("Citation"),
 *   bundle_label = @Translation("Citation type"),
 *   handlers = {
 *     "view_builder" = "Drupal\stanford_publication\CitationViewBuilder",
 *     "access" = "Drupal\stanford_publication\CitationAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm"
 *     },
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 *   base_table = "citation",
 *   data_table = "citation_field_data",
 *   translatable = TRUE,
 *   permission_granularity = "bundle",
 *   admin_permission = "administer citation entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "title",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   bundle_entity_type = "citation_type",
 *   field_ui_base_route = "entity.citation_type.edit_form"
 * )
 */
class Citation extends ContentEntityBase implements CitationInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function setLabel($name): CitationInterface {
    $this->set('title', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDescription(t('The title of the Citation.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['parent_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Parent ID'))
      ->setDescription(t('The ID of the parent entity of which this entity is referenced.'))
      ->setSetting('is_ascii', TRUE);

    $fields['parent_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Parent type'))
      ->setDescription(t('The entity parent type to which this entity is referenced.'))
      ->setSetting('is_ascii', TRUE)
      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH);

    $fields['parent_field_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Parent field name'))
      ->setDescription(t('The entity parent field name to which this entity is referenced.'))
      ->setSetting('is_ascii', TRUE)
      ->setSetting('max_length', FieldStorageConfig::NAME_MAX_LENGTH);

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function getParentEntity() {
    if (!isset($this->get('parent_type')->value) || !isset($this->get('parent_id')->value)) {
      return NULL;
    }

    $parent = $this->entityTypeManager()
      ->getStorage($this->get('parent_type')->value)
      ->load($this->get('parent_id')->value);

    // Return current translation of parent entity, if it exists.
    if (
      $parent != NULL &&
      ($parent instanceof TranslatableInterface) &&
      $parent->hasTranslation($this->language()->getId())
    ) {
      return $parent->getTranslation($this->language()->getId());
    }

    return $parent;
  }

  /**
   * {@inheritdoc}
   */
  public function setParentEntity(ContentEntityInterface $parent, $parent_field_name) {
    $this->set('parent_type', $parent->getEntityTypeId());
    $this->set('parent_id', $parent->id());
    $this->set('parent_field_name', $parent_field_name);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getBibliography($style = self::APA): string {

    $data = [
      'id' => $this->id(),
      'title' => $this->label(),
      // Custom variables that wrap the title with a link tag.
      'link-beginning' => $this->getLinkBeginning(),
      'link-ending' => $this->getLinkBeginning() ? '</a>' : NULL,
      'DOI' => $this->getDoi(),
      'URL' => $this->getUrl(),
      'author' => $this->getAuthor(),
      'edition' => (int) $this->getEdition(),
      'issue' => $this->getIssue(),
      'issued' => $this->getDate(),
      'genre' => $this->getGenre(),
      'page' => $this->getPage(),
      'publisher' => $this->getPublisher(),
      'publisher-place' => $this->getPublisherPlace(),
      'subtitle' => $this->getSubtitle(),
      'type' => $this->getType(),
      'volume' => (int) $this->getVolume(),
    ];

    if ($data['type'] == 'article-journal') {
      $data['collection-title'] = $data['publisher'];
    }

    // Convert the arrays into objects.
    $data = json_decode(json_encode([array_filter($data)]));

    $local_csl = __DIR__ . '/Styles/' . $style . '.xml';
    if (!file_exists($local_csl)) {
      return '';
    }

    // Load the style CSL file.
    $style = file_get_contents($local_csl);
    $citeProc = new CiteProc($style);
    return htmlspecialchars_decode($citeProc->render($data));
  }

  /**
   * If the citation can be linked to a url, get the first part of the <a> tag.
   *
   * @return string|null
   *   First half of the <a> tag.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  protected function getLinkBeginning() {
    if ($link = $this->getLink()) {
      // This will pull out the `<a href....>` part of the link.
      preg_match('/<a.*?>/', (string) $link->toString(), $matches);
      return $matches[0] ?? NULL;
    }
  }

  /**
   * Get the label of the entity wrapped in a link tag to the parent or url.
   *
   * @return \Drupal\Core\Link|null
   *   Label or linked label string.
   *
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  protected function getLink() {
    $url = NULL;

    // Link to the parent node.
    if ($parent_entity = $this->getParentEntity()) {
      $url = $parent_entity->toUrl();
    }

    // The user entered url.
    if ($url_string = $this->getUrl()) {
      $url = $this->getUrlFromString($url_string) ?? $url;
    }

    if ($url) {
      return Link::fromTextAndUrl('[replace]', $url);
    }
  }

  /**
   * Get a url object from the provided url/string.
   *
   * @param string $string
   *   User entered string.
   *
   * @return \Drupal\Core\Url|null
   *   Url object if successful.
   */
  protected function getUrlFromString($string): ?Url {
    try {
      return Url::fromUserInput($string);
    }
    catch (\Exception $e) {
      try {
        return Url::fromUri($string);
      }
      catch (\Exception $e) {
        // Nothing to do, just fall back to returning null.
      }
    }
    return NULL;
  }

  /**
   * Fallback function to get the entity field's string value.
   *
   * @param string $name
   *   Function name.
   * @param mixed $args
   *   Args.
   *
   * @return string
   *   Entity field value as a string.
   */
  public function __call($name, $args) {
    // Remove the `get` from the beginning.
    $data_name = preg_replace('/^get/', '', $name);

    // Convert UpperCamelCase to snake_case. This allows us to dynamically
    // fetch field names just by using the method names. Later versions it would
    // be preferred to have a UI that allows the user to choose which field
    // maps to which variable in the CSL.
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $data_name, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
      $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    $data_name = implode('_', $ret);

    if ($field = $this->getFieldName($data_name)) {
      return $this->get($field)->getString();
    }
  }

  /**
   * Get the list of authors from the entity field.
   *
   * @return array|null
   *   Keyed array of author data.
   */
  protected function getAuthor() {
    // Authors are rendered using `names` render element in CSL. It expects
    // the name array to be keyed with `given`, `family` etc keys. Luckily the
    // name module does this for us.
    // @link https://docs.citationstyles.org/en/1.0.1/specification.html#names
    if ($field = $this->getFieldName('author')) {
      return $this->get($field)->getValue();
    }
  }

  /**
   * Get the type of citation being used.
   *
   * @return string
   *   Citation type.
   */
  protected function getType(): string {
    return $this->entityTypeManager()
      ->getStorage('citation_type')
      ->load($this->bundle())
      ->type();
  }

  /**
   * Get the structured date array from the entity.
   *
   * The structure of a date field is an associate array with the year, month,
   * day in that order. We have to construct the array in a way that doesn't
   * return the year and day without the month.
   *
   * @link https://docs.citationstyles.org/en/1.0.1/specification.html#date
   *
   * @return array|null
   *   Keyed array of date parts.
   */
  protected function getDate() {
    $year = (int) $this->getYear();
    $month = (int) $this->getMonth();
    $day = (int) $this->getDay();

    if ($year) {
      $date_parts = [$year, $month];

      // The 2nd value has to be the month. If the user populates the year,
      // month and day, then we'll structure it correctly. Otherwise we leave
      // the day off and if the month is also empty, it'll be stripped in the
      // array filter below.
      if ($month && $day) {
        $date_parts = [$year, $month, $day];
      }

      return [
        'date-parts' => [array_filter($date_parts)],
      ];
    }
  }

  /**
   * Get the name of the field that is associated to the the attribute value.
   *
   * @param string $attribute
   *   Citation attribute key.
   *
   * @return string|null
   *   Field name if a field exists.
   */
  protected function getFieldName($attribute) {
    $field_name = "su_$attribute";
    // Later versions this will be a field mapping on the entity type config.
    if ($field_name && $this->hasField($field_name)) {
      return $field_name;
    }
  }

}
