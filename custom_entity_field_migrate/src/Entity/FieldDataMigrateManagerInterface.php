<?php

namespace Drupal\custom_entity_field_migrate\Entity;

/**
 * Interface for field data migration managers.
 */
interface FieldDataMigrateManagerInterface {

  /**
   * Set a field to the given value(s).
   *
   * @param string $field_id
   *   An entity field ID.
   * @param string $entity_type_id
   *   An entity type ID.
   * @param mixed $values
   *   Either a singular or a set of property-value pairs.
   *   If scalar the field's main property will be set to the given value.
   *   If multiple, each property will be set to the corresponding value.
   * @param array $only
   *   (optional) An array of entity IDs of the given entity type, that determine which
   *   entity instances the value assignments will apply to. If omitted, values
   *   will be applied to all entities (Default).
   * @param string|null $bundle
   *   (optional) An entity bundle.
   */
  public function set(string $field_id, string $entity_type_id, array $values, array $only = [], string $bundle = NULL);

  /**
   * Copies data from one field to another.
   *
   * @param string $source_field_id
   *   The ID of the field from which to read.
   * @param string $target_field_id
   *   The ID of the field to which to write.
   * @param string $entity_type_id
   *   The ID of the source and target fields' entity type.
   * @param string|null $bundle
   *   (optional) The source and target fields' bundle.
   * @param array $options
   *   (optional) Accepts the following additional keys:
   *     - start_transaction: (bool) Whether to start a new transaction
   *     when executing the migration. Defaults to FALSE.
   */
  public function copy(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL, array $options = []);

  /**
   * Copies data from one field to another.
   *
   * @param string $source_field_id
   *   The ID of the field from which to read.
   * @param string $target_field_id
   *   The ID of the field to which to write.
   * @param string $entity_type_id
   *   The ID of the source and target fields' entity type.
   * @param string|null $bundle
   *   (optional) The source and target fields' bundle.
   * @param array $options
   *   (optional) Accepts the following additional keys:
   *     - start_transaction: (bool) Whether to start a new transaction
   *     when executing the migration. Defaults to TRUE.
   */
  public function move(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL, array $options = []);

  /**
   * Deletes field data.
   *
   * @param string $field_id
   *   The ID of the field.
   * @param string $entity_type_id
   *   The ID of the field's entity type.
   * @param string|null $bundle
   *   (optional) The field's bundle.
   * @param array $options
   *   (optional) Accepts the following additional keys:
   *     - start_transaction: (bool) Whether to start a new transaction
   *     when executing the migration. Defaults to TRUE.
   */
  public function delete(string $field_id, string $entity_type_id, string $bundle = NULL, array $options = []);

}
