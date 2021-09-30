<?php

namespace Drupal\custom_entity\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Adds "created" and/or "creator" fields to content entities.
 */
trait EntityCreatedTrait {

  /**
   * {@inheritDoc}
   */
  public function getCreatedTime() {
    if ($created = $this->get(static::FIELD_CREATED)) {
      return $created->value;
    }
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function getCreator() {
    if ($creator = $this->get(static::FIELD_CREATOR)) {
      return $creator->entity;
    }
    return FALSE;
  }

  /**
   * Provide a base field definition for a "created" field.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   A base field definition.
   */
  protected static function getCreatedBaseFieldDefinition(EntityTypeInterface $entity_type) {
    return BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t("Timestamp indicating the @entity's creation.", [
        '@entity' => $entity_type->id(),
      ]));
  }

  /**
   * Provide a base field definition for a "creator" field.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition
   *   A base field definition.
   */
  protected static function getCreatorBaseFieldDefinition(EntityTypeInterface $entity_type) {
    return BaseFieldDefinition::create('creator')
      ->setLabel(t('Creator'))
      ->setDescription(t('The user who created the @entity.', [
        '@entity' => $entity_type->id(),
      ]));
  }

}
