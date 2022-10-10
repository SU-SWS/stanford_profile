<?php

namespace Drupal\stanford_policy\EventSubscriber;

use Drupal\book\BookManagerInterface;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\AbstractEntityEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Drupal\core_event_dispatcher\Event\Form\FormAlterEvent;
use Drupal\core_event_dispatcher\FormHookEvents;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\node\NodeInterface;
use Drupal\stanford_fields\Event\BookOutlineUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Stanford Policy event subscriber.
 */
class StanfordPolicySubscriber implements EventSubscriberInterface {

  /**
   * Flag to prevent recursion.
   *
   * @var bool
   */
  protected $alreadyHere = FALSE;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      BookOutlineUpdatedEvent::OUTLINE_UPDATED => 'onBookOutlineUpdate',
      FormHookEvents::FORM_ALTER => 'onFormAlter',
      EntityHookEvents::ENTITY_PRE_SAVE => 'onEntityPreSave',
      EntityHookEvents::ENTITY_UPDATE => 'onEntityCrud',
      EntityHookEvents::ENTITY_INSERT => 'onEntityCrud',
      EntityHookEvents::ENTITY_DELETE => 'onEntityCrud',
    ];
  }

  /**
   * Event subscriber constructor.
   *
   * @param \Drupal\book\BookManagerInterface $bookManager
   *   Book manager service.
   * @param \Drupal\config_pages\ConfigPagesLoaderServiceInterface $configPagesLoader
   *   Config page loader service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager service.
   */
  public function __construct(protected BookManagerInterface $bookManager, protected ConfigPagesLoaderServiceInterface $configPagesLoader, protected EntityTypeManagerInterface $entityTypeManager) {
  }

  /**
   * Resave all books if the policy config page was saved/deleted.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\AbstractEntityEvent $event
   *   Triggered event.
   */
  public function onEntityCrud(AbstractEntityEvent $event) {
    $entity = $event->getEntity();
    if ($entity->getEntityTypeId() == 'config_pages' && $entity->bundle() == 'policy_settings') {
      $book_node_ids = array_keys($this->bookManager->getAllBooks());
      foreach ($book_node_ids as $node_id) {
        $this->resaveBookNodes($node_id);
      }
    }
  }

  /**
   * Reset the policy node label from the other field.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent $event
   *   Triggered event.
   */
  public function onEntityPreSave(EntityPresaveEvent $event): void {
    $entity = $event->getEntity();
    // Since the settings for the auto entity label have to be "Preserve
    // Existing" so that we don't get errors, we still need to update the node
    // label if the field changed. Use the "Changed" field to determine if this
    // has already been done because the node will be re-saved with the book
    // outline update.
    if (
      $entity->getEntityTypeId() == 'node' &&
      $entity->bundle() == 'stanford_policy' &&
      $entity->getChangedTime() < time() - 5
    ) {
      $entity->set('title', trim($entity->get('su_policy_title')->getString()));
      $entity->setChangedTime(time());
    }
  }

  /**
   * Alter the book admin form to add submit handler.
   *
   * @param \Drupal\core_event_dispatcher\Event\Form\FormAlterEvent $event
   *   Triggered Event.
   */
  public function onFormAlter(FormAlterEvent $event): void {
    if ($event->getFormId() == 'book_admin_edit') {
      $build_args = $event->getFormState()->getBuildInfo()['args'];
      $book_node = $build_args[0];

      if ($book_node->bundle() == 'stanford_policy') {
        $form = &$event->getForm();
        $form['#submit'][] = [self::class, 'onBookAdminEditSubmit'];
      }
    }
    if (in_array($event->getFormId(), [
      'node_stanford_policy_form',
      'node_stanford_policy_edit_form',
    ])) {
      $form = &$event->getForm();
      $form['su_policy_title']['#attributes']['class'][] = 'js-form-item-title-0-value';
    }
  }

  /**
   * Dispatch the event to update the book outline.
   *
   * @param array $form
   *   Complete form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Submitted form state.
   */
  public static function onBookAdminEditSubmit(array &$form, FormStateInterface $form_state): void {
    $build_args = $form_state->getBuildInfo()['args'];
    $book_node = $build_args[0];
    \Drupal::service('event_dispatcher')
      ->dispatch(new BookOutlineUpdatedEvent($book_node), BookOutlineUpdatedEvent::OUTLINE_UPDATED);
  }

  /**
   * After the book outline is updated, re-save node titles to match.
   *
   * @param \Drupal\stanford_fields\Event\BookOutlineUpdatedEvent $event
   *   Triggered event.
   */
  public function onBookOutlineUpdate(BookOutlineUpdatedEvent $event) {
    if ($this->alreadyHere) {
      return;
    }

    $this->alreadyHere = TRUE;
    if ($book_id = $event->getUpdatedBookId()) {
      $this->resaveBookNodes($book_id);
    }
  }

  /**
   * Traverse the book and modify and resave all nodes.
   *
   * @param int $book_id
   *   Book node id.
   */
  protected function resaveBookNodes(int $book_id): void {
    $book_contents = $this->bookManager->getTableOfContents($book_id, 9);
    foreach (array_keys($book_contents) as $nid) {
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      $previous_title = $node->label();
      $this->modifyPolicyEntity($node);
      if ($node->label() != $previous_title) {
        $node->save();
      }
    }
  }

  /**
   * Modify the fields and the label on policy nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node entity.
   */
  public function modifyPolicyEntity(NodeInterface $node): void {
    // Book settings not set.
    if (empty($node->book['pid'])) {
      return;
    }

    if ($node->get('su_policy_auto_prefix')->getString()) {
      $node->set('su_policy_chapter', NULL);
      $node->set('su_policy_subchapter', NULL);
      $node->set('su_policy_policy_num', NULL);

      foreach ($this->getAutomaticPrefix($node) as $field => $value) {
        $node->set($field, $value);
      }
    }

    /** @var \Drupal\node\NodeInterface $entity */
    $prefix = [
      $node->get('su_policy_chapter')->getString(),
      $node->get('su_policy_subchapter')->getString(),
      $node->get('su_policy_policy_num')->getString(),
    ];
    $prefix = array_filter($prefix);
    if (count($prefix) == 1) {
      $prefix[] = '';
    }

    $title = implode('.', $prefix);
    $title .= ' ' . $node->get('su_policy_title')->getString();
    $node->set('title', trim($title));
  }

  /**
   * Get the prefix field values that will be used for the title.
   *
   * @param \Drupal\node\NodeInterface $node
   *   Node entity.
   *
   * @return array
   *   Keyed array of field names => values.
   */
  protected function getAutomaticPrefix(NodeInterface $node): array {
    $book_link = $this->bookManager->loadBookLink($node->id());
    if (!$book_link) {
      return [];
    }
    $prefix_strings = $this->getLinkPrefix($book_link, $node->id());

    $field_names = [
      'su_policy_chapter',
      'su_policy_subchapter',
      'su_policy_policy_num',
    ];

    $field_names = array_slice($field_names, 0, count($prefix_strings));
    return array_combine($field_names, array_slice($prefix_strings, 0, 3));
  }

  /**
   * Get the prefix array for the given book link.
   *
   * @param array $book_link
   *   Book link keyed array.
   * @param int $node_id
   *   Node entity id.
   *
   * @return array
   *   Associative array of prefix strings.
   */
  protected function getLinkPrefix(array $book_link, int $node_id): array {
    if (!$book_link['pid']) {
      return [];
    }
    $parent_book_link = $this->bookManager->loadBookLink($book_link['pid']);
    $parent_tree = $this->bookManager->bookSubtreeData($parent_book_link);

    $position = 1;
    foreach (reset($parent_tree)['below'] as $sibling) {
      if ($sibling['link']['nid'] == $node_id) {
        break;
      }
      $position++;
    }

    if ($book_link['pid'] != $book_link['bid']) {
      $parent_node = $this->entityTypeManager->getStorage('node')
        ->load($book_link['pid']);

      preg_match('/^.*? /', $parent_node->label(), $parent_prefix);
      $prefix = [trim(reset($parent_prefix), ' .')];
    }

    $prefix[] = $this->getPrefix($book_link['depth'], $position);
    return $prefix;
  }

  /**
   * Use state to allow customizing which characters are used for the prefix.
   *
   * @param int $depth
   *   Depth level 1-9.
   * @param int $position
   *   Position in the given depth level.
   *
   * @return string
   *   Character(s) prefix to use.
   */
  protected function getPrefix(int $depth, int $position): string {
    $field_name = $depth == 2 ? 'su_policy_prefix_first' : ($depth == 3 ? 'su_policy_prefix_sec' : 'su_policy_prefix_third');
    $prefix_set = $this->configPagesLoader->getValue('policy_settings', $field_name, 0, 'value');

    $letters = range('A', 'Z');

    switch ($prefix_set) {
      case 'alpha_uppercase':
        return $letters[$position - 1];

      case 'alpha_lowercase':
        return strtolower($letters[$position - 1]);

      case 'roman_numeral_uppercase':
        return $this->getRomanNumeral($position);

      case 'roman_numeral_lowercase':
        return strtolower($this->getRomanNumeral($position));
    }

    return $position;
  }

  /**
   * Get the roman numeral representation of a number.
   *
   * @param int $num
   *   Number to convert to roman numeral.
   *
   * @return string
   *   Roman numeral.
   *
   * @link https://stackoverflow.com/questions/14994941/numbers-to-roman-numbers-with-php
   */
  protected function getRomanNumeral(int $num): string {
    $map = [
      'M' => 1000,
      'CM' => 900,
      'D' => 500,
      'CD' => 400,
      'C' => 100,
      'XC' => 90,
      'L' => 50,
      'XL' => 40,
      'X' => 10,
      'IX' => 9,
      'V' => 5,
      'IV' => 4,
      'I' => 1,
    ];
    $returnValue = '';
    while ($num > 0) {
      foreach ($map as $roman => $int) {
        if ($num >= $int) {
          $num -= $int;
          $returnValue .= $roman;
          break;
        }
      }
    }
    return $returnValue;
  }

}
