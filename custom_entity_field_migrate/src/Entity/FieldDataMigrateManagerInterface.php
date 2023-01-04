<?php

namespace Drupal\custom_entity_field_migrate\Entity;

/**
 * Interface for field data migration managers.
 */
interface FieldDataMigrateManagerInterface {

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
