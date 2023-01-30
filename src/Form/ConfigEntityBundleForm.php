<?php

namespace Drupal\custom_entity\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Config entity form for bundles of content entities.
 */
class ConfigEntityBundleForm extends ConfigEntityForm {

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $entity = $this->getEntity();

    if (in_array('bundle_class', $entity->getEntityType()->getPropertiesToExport($entity->id()))) {
      $form['bundle_class'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Bundle class'),
        '#default_value' => $entity->get('bundle_class'),
      ];
    }

    return $form;
  }

}
