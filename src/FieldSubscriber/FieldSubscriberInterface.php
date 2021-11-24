<?php

namespace Drupal\custom_entity\FieldSubscriber;

/**
 * Interface for 'field_subscriber' services.
 */
interface FieldSubscriberInterface {

  /**
   * The entity type ID.
   *
   * @return string
   *   A string.
   */
  public function getEntityTypeId();

  /**
   * Alter the fields of the entity.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface[] $fields
   *   An array of entity field definitions.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface[]
   *   An array of altered entity field definitions.
   */
  public function alter(array &$fields);

}
