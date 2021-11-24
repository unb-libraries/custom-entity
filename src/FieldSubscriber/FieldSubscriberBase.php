<?php

namespace Drupal\custom_entity\FieldSubscriber;

use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Base implementation for custom 'field_subscriber' services.
 */
abstract class FieldSubscriberBase implements FieldSubscriberInterface {

  /**
   * The ID of the entity type the subscriber subscribes to.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * {@inheritDoc}
   */
  public function getEntityTypeId() {
    return $this->entityTypeId;
  }

  /**
   * Create a new FieldSubscriberBase instance.
   *
   * @param string $entity_type_id
   *   An entity type ID string.
   */
  public function __construct(string $entity_type_id) {
    $this->entityTypeId = $entity_type_id;
  }

  /**
   * {@inheritDoc}
   */
  public function alter(array &$fields) {
    foreach ($fields as &$field) {
      if (is_callable($alter = $this->buildFieldAlterCallback($field))) {
        $field = call_user_func($alter, $field);
      }
    }
    return $fields;
  }

  /**
   * Build the callback for a custom field alterer.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field
   *   A field definition.
   *
   * @return callable
   *   A callback.
   */
  protected function buildFieldAlterCallback(FieldDefinitionInterface $field) {
    $field_id = implode('', array_map(function ($piece) {
      return $piece !== 'field'
        ? ucfirst(strtolower($piece))
        : '';
    }, explode('_', $field->getName())));
    return [$this, "alter{$field_id}"];


  }


}
