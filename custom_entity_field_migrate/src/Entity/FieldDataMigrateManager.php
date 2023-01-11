<?php

namespace Drupal\custom_entity_field_migrate\Entity;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityFieldManagerInterface;
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
   * The field mapping factory.
   *
   * @var \Drupal\custom_entity_field_migrate\Entity\FieldMappingFactoryInterface
   */
  protected $fieldMappingFactory;

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
   * Get the field mapping factory.
   *
   * @return \Drupal\custom_entity_field_migrate\Entity\FieldMappingFactoryInterface
   *   A field mapping factory.
   */
  protected function fieldMappingFactory() {
    return $this->fieldMappingFactory;
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
   * @param \Drupal\custom_entity_field_migrate\Entity\FieldMappingFactoryInterface $mapping_factory
   *   A field mapping factory.
   */
  public function __construct(Connection $db, EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, FieldMappingFactoryInterface $mapping_factory) {
    $this->db = $db;
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->fieldMappingFactory = $mapping_factory;
  }

  /**
   * {@inheritDoc}
   */
  public function set(string $field_id, string $entity_type_id, array $values, array $only = [], string $bundle = NULL) {
    $schema = $this->fieldMappingFactory()->createSchema($field_id, $entity_type_id, $bundle);

    $rows_updated = 0;
    foreach ($schema->getTables() as $table) {
      $fields = array_map(function ($property) use ($values) {
        return $values[$property];
      }, array_flip($schema->getPropertyColumnMap()));

      $query = $this->db()
        ->update($table)
        ->fields($fields);

      if ($only) {
        $query->condition($schema->getKey($table), $only, 'IN');
      }
      $rows_updated += $query
        ->execute();
    }

    return $rows_updated;
  }


  /**
   * {@inheritDoc}
   */
  public function copy(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL, array $options = []) {
    $mappings = $this->fieldMappingFactory()->create($source_field_id, $target_field_id, $entity_type_id);
    foreach ($mappings as $mapping) {
      $data = $this->db()
        ->select($mapping->getSourceTable())
        ->fields($mapping->getSourceTable(), array_merge(array_keys($mapping->getKeyMap()), array_keys($mapping->getColumnMap())))
        ->orderBy(array_keys($mapping->getKeyMap())[0])
        ->execute()
        ->fetchAllAssoc(array_keys($mapping->getKeyMap())[0]);

      foreach ($data as $values) {
        $fields = array_map(function ($source_field) use ($values) {
          return $values->$source_field;
        }, array_flip($mapping->getColumnMap()));

        if ($mapping->getMethod() === FieldMappingInterface::METHOD_UPDATE) {
          $query = $this->db()
            ->update($mapping->getTargetTable())
            ->fields($fields);
          foreach ($mapping->getKeyMap() as $source_key => $target_key) {
            $query->condition($target_key, $values->$source_key);
          }
        }
        else {
          $query = $this->db()
            ->insert($mapping->getTargetTable())
            ->fields($fields);
        }
        $query->execute();
      }
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
