<?php

namespace Drupal\Tests\stanford_publication\Kernel\Form;

use Drupal\Core\Form\FormState;
use Drupal\stanford_publication\Entity\CitationType;
use Drupal\Tests\stanford_publication\Kernel\PublicationTestBase;

/**
 * Class CitationTypeFormTest
 *
 * @group stanford_publication
 * @coversDefaultClass \Drupal\stanford_publication\Form\CitationTypeForm
 */
class CitationTypeFormTest extends PublicationTestBase {

  /**
   * Check the Citation entity type form.
   */
  public function testEntityTypeForm() {
    $citation_type = CitationType::create([
      'id' => 'su_foo',
      'label' => 'Foo',
    ]);

    $form_object = \Drupal::entityTypeManager()
      ->getFormObject('citation_type', 'edit');
    $form = [];
    $form_state = new FormState();
    $form_object->setEntity($citation_type);
    $form = $form_object->buildForm($form, $form_state);

    $this->assertArrayHasKey('id',$form);
    $this->assertArrayHasKey('label',$form);
    $this->assertArrayHasKey('type',$form);

    $form_object->save($form, $form_state);
    $this->assertEquals('/admin/structure/citation_type',$form_state->getRedirect()->toString());

    $form_object->setEntity(CitationType::load('su_foo'));
    $form_object->save($form, $form_state);
    $this->assertEquals('/admin/structure/citation_type',$form_state->getRedirect()->toString());
  }

}
