<?php

namespace Drupal\stanford_publication\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class CitationTypeForm.
 */
class CitationTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $citation_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $citation_type->label(),
      '#description' => $this->t("Label for the Citation type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $citation_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\stanford_publication\Entity\CitationType::load',
      ],
      '#disabled' => !$citation_type->isNew(),
    ];

    $link = Link::fromTextAndUrl('CSL documentation', Url::fromUri('https://docs.citationstyles.org/en/1.0.1/specification.html#appendix-iii-types'));
    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Citation Type'),
      '#description' => $this->t('Based on the CSL type declaration. See %link', ['%link' => $link->toString()]),
      '#options' => [
        'article' => 'article',
        'article-magazine' => 'article-magazine',
        'article-newspaper' => 'article-newspaper',
        'article-journal' => 'article-journal',
        'bill' => 'bill',
        'book' => 'book',
        'broadcast' => 'broadcast',
        'chapter' => 'chapter',
        'dataset' => 'dataset',
        'entry' => 'entry',
        'entry-dictionary' => 'entry-dictionary',
        'entry-encyclopedia' => 'entry-encyclopedia',
        'figure' => 'figure',
        'graphic' => 'graphic',
        'interview' => 'interview',
        'legislation' => 'legislation',
        'legal_case' => 'legal_case',
        'manuscript' => 'manuscript',
        'map' => 'map',
        'motion_picture' => 'motion_picture',
        'musical_score' => 'musical_score',
        'other' => 'other',
        'pamphlet' => 'pamphlet',
        'paper-conference' => 'paper-conference',
        'patent' => 'patent',
        'post' => 'post',
        'post-weblog' => 'post-weblog',
        'personal_communication' => 'personal_communication',
        'report' => 'report',
        'review' => 'review',
        'review-book' => 'review-book',
        'song' => 'song',
        'speech' => 'speech',
        'thesis' => 'thesis',
        'treaty' => 'treaty',
        'webpage' => 'webpage',
      ],
      '#default_value' => $citation_type->type(),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $citation_type = $this->entity;
    $status = $citation_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()
          ->addMessage($this->t('Created the %label Citation type.', [
            '%label' => $citation_type->label(),
          ]));
        break;

      default:
        $this->messenger()
          ->addMessage($this->t('Saved the %label Citation type.', [
            '%label' => $citation_type->label(),
          ]));
    }
    $form_state->setRedirectUrl($citation_type->toUrl('collection'));
  }

}
