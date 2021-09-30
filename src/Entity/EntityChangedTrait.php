<?php

namespace Drupal\custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Adds "changed" and/or "editor" fields to content entities.
 *
 * @package Drupal\custom_entity\Entity
 */
trait EntityChangedTrait {

  /**
   * {@inheritDoc}
   */
  public function getEditedTime() {
    if ($changed_time = $this->get(UserEditedInterface::FIELD_EDITED)) {
      return $changed_time->value;
    }
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function setEditedTime($timestamp) {
    if ($this->get(UserEditedInterface::FIELD_EDITED)) {
      $this->set(UserEditedInterface::FIELD_EDITED, $timestamp);
    }
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getEditor() {
    if ($user = $this->get(UserEditedInterface::FIELD_EDITOR)) {
      return $user->entity;
    }
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function setEditor(AccountInterface $user) {
    if ($this->get(UserEditedInterface::FIELD_EDITOR)) {
      $this->set(UserEditedInterface::FIELD_EDITOR, $user);
    }
    return $this;
  }

  /**
   * Base field definition for an "editor" field.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   A base field definition.
   */
  protected static function getEditorBaseFieldDefinition(EntityTypeInterface $entity_type) {
    return BaseFieldDefinition::create('editor')
      ->setLabel(t('Editor'))
      ->setDescription(t('The user who last edited the entity.'))
      ->setRevisionable($entity_type->isRevisionable());
  }

  /**
   * Base field definition for a "changed" field.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   A base field definition.
   */
  protected static function getEditedBaseFieldDefinition(EntityTypeInterface $entity_type) {
    return BaseFieldDefinition::create('changed')
      ->setLabel(t('Edited'))
      ->setDescription(t("Timestamp indicating the @entity's most recent edit.", [
        '@entity' => $entity_type->id(),
      ]))
      ->setRevisionable($entity_type->isRevisionable());
  }



}
