<?php

namespace Drupal\custom_entity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base form for config entities.
 */
class ConfigEntityForm extends EntityForm {

  /**
   * The storage handler.
   *
   * @var \Drupal\Core\Config\Entity\ConfigEntityStorageInterface
   */
  protected $storage;

  /**
   * Get the storage handler.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface
   *   A config entity storage handler.
   *
   * @noinspection PhpUnhandledExceptionInspection
   * @noinspection PhpDocMissingThrowsInspection
   */
  protected function storage() {
    if (!isset($this->storage)) {
      $entity_type_id = $this->getEntity()->getEntityTypeId();
      $this->storage = $this
        ->entityTypeManager
        ->getStorage($entity_type_id);
    }
    return $this->storage;
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $entity = $this->getEntity();

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#required' => TRUE,
      '#default_value' => $entity->label(),
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => [$this, 'doesExist']
      ],
      '#disabled' => !$entity->isNew(),
    ];

    return $form;
  }

  /**
   * Check whether an entity with the given ID exists.
   *
   * @param string $id
   *   An entity ID.
   *
   * @return bool
   *   TRUE if an entity with the given ID exists. FALSE otherwise.
   */
  public function doesExist(string $id) {
    return $this
        ->storage()
        ->getQuery()
        ->condition('id', $id)
        ->count()
        ->execute() > 0;
  }

}
