<?php

namespace Drupal\stanford_policy\EventSubscriber;

use Drupal\book\BookManagerInterface;
use Drupal\config_pages\ConfigPagesLoaderServiceInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityLoadEvent;
use Drupal\node\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Stanford Policy event subscriber.
 */
class StanfordPolicySubscriber implements EventSubscriberInterface {

  /**
   * List of previously modified entity ids.
   *
   * @var array
   */
  protected $previouslyModified = [];

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      EntityHookEvents::ENTITY_LOAD => 'onEntityLoad',
    ];
  }

  /**
   * Event subscriber constructor.
   *
   * @param \Drupal\book\BookManagerInterface $bookManager
   *   Book manager service.
   * @param \Drupal\config_pages\ConfigPagesLoaderServiceInterface $configPagesLoader
   *   Config page loader service.
   */
  public function __construct(protected BookManagerInterface $bookManager, protected ConfigPagesLoaderServiceInterface $configPagesLoader) {
  }

  /**
   * Event listener to modify the policy node.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityLoadEvent $event
   *   Triggered event.
   */
  public function onEntityLoad(EntityLoadEvent $event): void {
    $entities = $event->getEntities();
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    foreach ($entities as $entity) {
      if (
        !in_array($entity->id(), $this->previouslyModified) &&
        $entity->getEntityTypeId() == 'node' &&
        $entity->bundle() == 'stanford_policy'
      ) {
        $this->previouslyModified[] = $entity->id();
        $this->modifyPolicyEntity($entity);
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

    $prefix = $this->getLinkPrefix($this->bookManager->loadBookLink($book_link['pid']), $parent_book_link['nid']);
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
