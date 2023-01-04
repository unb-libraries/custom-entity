<?php

namespace Drupal\custom_entity_field_migrate\Entity;

/**
 * Copies, moves, or deletes field data.
 */
class FieldDataMigrateManager implements FieldDataMigrateManagerInterface {

  /**
   * {@inheritDoc}
   */
  public function copy(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL, array $options = []) {
    // TODO: Implement copy() method.
  }

  /**
   * {@inheritDoc}
   */
  public function move(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL, array $options = []) {
    // TODO: Implement move() method.
  }

  /**
   * {@inheritDoc}
   */
  public function delete(string $field_id, string $entity_type_id, string $bundle = NULL, array $options = []) {
    // TODO: Implement delete() method.
  }

}
