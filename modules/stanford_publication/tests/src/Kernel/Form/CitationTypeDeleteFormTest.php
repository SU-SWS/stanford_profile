<?php

namespace Drupal\Tests\stanford_publication\Kernel\Form;

use Drupal\Core\Form\FormState;
use Drupal\stanford_publication\Entity\CitationType;
use Drupal\Tests\stanford_publication\Kernel\PublicationTestBase;

/**
 * Class CitationTypeDeleteFormTest.
 *
 * @group stanford_publication
 * @coversDefaultClass \Drupal\stanford_publication\Form\CitationTypeDeleteForm
 */
class CitationTypeDeleteFormTest extends PublicationTestBase {

  /**
   * Test the delete from methods and submission.
   */
  public function testFormQuestions() {
    $citation = CitationType::create(['id' => 'su_foo', 'label' => 'Foo']);
    $citation->save();
    $form_object = \Drupal::entityTypeManager()
      ->getFormObject('citation_type', 'delete');
    $form_object->setEntity($citation);

    $this->assertStringContainsString('>Foo<', (string) $form_object->getQuestion());
    $this->assertEquals('/admin/structure/citation_type', $form_object->getCancelUrl()
      ->toString());
    $this->assertNotEmpty((string) $form_object->getConfirmText());

    $citation_id = $citation->id();
    $form = [];
    $form_state = new FormState();
    $form_object->submitForm($form, $form_state);

    $this->assertEmpty(\Drupal::entityTypeManager()
      ->getStorage('citation')
      ->load($citation_id));

  }

}
