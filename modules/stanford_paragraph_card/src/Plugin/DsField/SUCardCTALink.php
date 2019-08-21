<?php
namespace Drupal\stanford_paragraph_card\Plugin\DsField;

use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Plugin that renders the link as plain text.
 *
 * @DsField(
 *   id = "su_card_cta_link",
 *   title = @Translation("su_card_cta_link url as plain text"),
 *   entity_type = "paragraph",
 *   ui_limit = "stanford_paragraph_card"
 * )
 */
class SUCardCTALink extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      "#markup" => "Hey Jude."
    ];
  }

}
