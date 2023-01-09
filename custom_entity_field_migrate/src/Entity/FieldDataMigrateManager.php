<?php

namespace Drupal\custom_entity_field_migrate\Entity;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Copies, moves, or deletes field data.
 */
class FieldDataMigrateManager implements FieldDataMigrateManagerInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $db;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Get the database connection.
   *
   * @return \Drupal\Core\Database\Connection
   *   A database connection instance.
   */
  protected function db() {
    return $this->db;
  }

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager instance.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Get the entity field manager.
   *
   * @return \Drupal\Core\Entity\EntityFieldManagerInterface
   *   An entity field manager instance.
   */
  protected function entityFieldManager() {
    return $this->entityFieldManager;
  }

  /**
   * Construct a new field data migrate manager instance.
   *
   * @param \Drupal\Core\Database\Connection $db
   *   A database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   An entity field manager.
   */
  public function __construct(Connection $db, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    $this->db = $db;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function copy(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL, array $options = []) {
    /** @var \Drupal\Core\Entity\Sql\SqlEntityStorageInterface $storage */
    $storage = $this->entityTypeManager()->getStorage($entity_type_id);
    /** @var \Drupal\Core\Entity\ContentEntityTypeInterface $entity_type */
    $entity_type = $storage->getEntityType();

    /** @var \Drupal\Core\Entity\Sql\DefaultTableMapping $source_map */
    $source_map = $storage->getTableMapping();
    $source_field_definition = $this->entityFieldManager()
      ->getFieldStorageDefinitions($entity_type_id)[$source_field_id];

    $source_table = $source_map->getDedicatedDataTableName($source_field_definition);
    /** @var \Drupal\Core\Entity\Sql\DefaultTableMapping $target_map */
    $target_map = $storage->getTableMapping();
    $target_field_definition = $this->entityFieldManager()
      ->getFieldStorageDefinitions($entity_type_id)[$target_field_id];
    $target_table = $target_map->getFieldTableName($target_field_id);

    $column_map = [];
    foreach ($source_field_definition->getPropertyNames() as $property_name) {
      $source_column_name = $source_map->getFieldColumnName($source_field_definition, $property_name);
      $target_column_name = $target_map->getFieldColumnName($target_field_definition, $property_name);
      $column_map[$source_column_name] = $target_column_name;
    }

    $this->doCopy($entity_type, $source_table, $target_table, $column_map);
  }

  protected function doCopy(EntityTypeInterface $entity_type, $source_table, $target_table, $column_map) {
    $data = $this->db()
      ->select($source_table)
      ->fields($source_table, array_merge('entity_id', array_keys($column_map)))
      ->orderBy('entity_id')
      ->execute()
      ->fetchAllAssoc('entity_id');

    foreach ($data as $id => $values) {
      $fields = array_map(function ($source_field) use ($values) {
        return $values->$source_field;
      }, array_flip($column_map));
      $this->db()
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
    $this->copy($source_field_id, $target_field_id, $entity_type_id);
    $this->delete($source_field_id, $entity_type_id);
  }

  /**
   * {@inheritDoc}
   */
  public function delete(string $field_id, string $entity_type_id, string $bundle = NULL, array $options = []) {
    /** @var \Drupal\Core\Entity\Sql\SqlEntityStorageInterface $storage */
    $storage = $this->entityTypeManager()->getStorage($entity_type_id);

    /** @var \Drupal\Core\Entity\Sql\DefaultTableMapping $field_map */
    $field_map = $storage->getTableMapping();
    $field_definition = $this->entityFieldManager()
      ->getFieldStorageDefinitions($entity_type_id)[$field_id];
    $table_name = $field_map->getFieldTableName($field_id);

    if ($field_definition->isBaseField()) {
      $column_name = $field_map
        ->getFieldColumnName($field_definition, $field_definition->getMainPropertyName());
      $this->db()
        ->update($table_name)
        ->fields([$column_name => NULL])
        ->execute();
    }
    else {
      $this->db()
        ->delete($table_name)
        ->execute();
    }
  }

}
