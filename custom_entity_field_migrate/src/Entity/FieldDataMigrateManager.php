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
    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager */
    $entity_field_manager = \Drupal::service('entity_field.manager');

    /** @var \Drupal\Core\Entity\Sql\SqlEntityStorageInterface $storage */
    $storage = $this->entityTypeManager()->getStorage($entity_type_id);
    /** @var \Drupal\Core\Entity\ContentEntityTypeInterface $entity_type */
    $entity_type = $storage->getEntityType();

    /** @var \Drupal\Core\Entity\Sql\DefaultTableMapping $source_map */
    $source_map = $storage->getTableMapping();
    $source_field_definition = $entity_field_manager->getFieldStorageDefinitions($entity_type_id)[$source_field_id];
    $source_table = $source_map->getDedicatedDataTableName($source_field_definition);
    $source_fields = array_merge(['entity_id'], $source_map->getColumnNames($source_field_id));
    $source_data = \Drupal::database()
      ->select($source_table)
      ->fields($source_table, $source_fields)
      ->orderBy('entity_id')
      ->execute()
      ->fetchAllAssoc('entity_id');

    /** @var \Drupal\Core\Entity\Sql\DefaultTableMapping $target_map */
    $target_map = $storage->getTableMapping();
    $target_field_definition = $entity_field_manager->getFieldStorageDefinitions($entity_type_id)[$target_field_id];
    $target_table = $target_map->getFieldTableName($target_field_id);

    $column_map = [];
    foreach ($source_field_definition->getPropertyNames() as $property_name) {
      $source_column_name = $source_map->getFieldColumnName($source_field_definition, $property_name);
      $target_column_name = $target_map->getFieldColumnName($target_field_definition, $property_name);
      $column_map[$source_column_name] = $target_column_name;
    }

    foreach ($source_data as $id => $values) {
      $fields = array_map(function ($source_field) use ($values) {
        return $values->$source_field;
      }, array_flip($column_map));
      \Drupal::database()
        ->update($target_table)
        ->condition($entity_type->getKey('id'), $id)
        ->fields($fields)
        ->execute();
    }
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
