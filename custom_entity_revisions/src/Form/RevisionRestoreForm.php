<?php

namespace Drupal\custom_entity_revisions\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form to restore to an entity to a previous revision.
 */
class RevisionRestoreForm extends ConfirmFormBase {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'entity_revision_restore_form';
  }

  /**
   * The revision.
   *
   * @var \Drupal\Core\Entity\RevisionableInterface
   */
  protected $revision;

  /**
   * Get the revision.
   *
   * @return \Drupal\Core\Entity\RevisionableInterface
   *   An entity revision.
   */
  protected function getRevision() {
    return $this->revision;
  }

  /**
   * {@inheritDoc}
   */
  public function getQuestion() {
    return $this->t('Do you want to restore this revision?');
  }

  /**
   * {@inheritDoc}
   */
  public function getCancelUrl() {
    return $this->getRevision()->toUrl('revisions');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Restore');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $_entity_revision = NULL) {
    $this->revision = $_entity_revision;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    $this->getRevision()->isDefaultRevision(TRUE);
    $this->getRevision()->save();
    $form_state->setRedirectUrl($this->getRevision()->toUrl());
  }
}
