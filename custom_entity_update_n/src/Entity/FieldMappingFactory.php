<?php

namespace Drupal\custom_entity_update_n\Entity;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Creates mappings between two entity fields.
 */
class FieldMappingFactory implements FieldMappingFactoryInterface {

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
   * Create a new FieldMappingFactory instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   An entity field manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function createSchema(string $field_id, string $entity_type_id, string $bundle = NULL) {
    $tables = [$this->deriveTableName($field_id, $entity_type_id)];
    $columns = $this->buildPropertyMap($field_id, $entity_type_id);
    $keys = [$tables[0] => $this->derivePrimaryKey($field_id, $entity_type_id)];

    if ($this->isRevisionable($field_id, $entity_type_id)) {
      $tables[] = $this->deriveTableName($field_id, $entity_type_id, TRUE);
      $keys[$tables[1]] = $this->derivePrimaryKey($field_id, $entity_type_id);
    }

    return new FieldSchema($tables, $columns, $keys);
  }


  /**
   * {@inheritDoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\Sql\SqlContentEntityStorageException
   */
  public function create(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL) {
    $mappings = [$this->doCreate($source_field_id, $target_field_id, $entity_type_id, $bundle)];
    if ($this->isRevisionable($source_field_id, $entity_type_id) && $this->isRevisionable($target_field_id, $entity_type_id)) {
      $mappings[] = $this->doCreate($source_field_id, $target_field_id, $entity_type_id, $bundle, TRUE);
    }
    return $mappings;
  }

  /**
   * Creates a mapping between two tables of the source and target fields.
   *
   * @param string $source_field_id
   *   The ID of the field acting as the source.
   * @param string $target_field_id
   *   The ID of the field acting as the target.
   * @param string $entity_type_id
   *   The ID of the entity type.
   * @param string|null $bundle
   *   (optional) The ID of the bundle.
   *
   * @return \Drupal\custom_entity_update_n\Entity\FieldMappingInterface
   *   A field mapping instance.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\Sql\SqlContentEntityStorageException
   */
  protected function doCreate(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL, $revision = FALSE) {
    $source_table = $this->deriveTableName($source_field_id, $entity_type_id, $revision);
    $target_table = $this->deriveTableName($target_field_id, $entity_type_id, $revision);

    $column_map = $this->buildColumnMap($source_field_id, $target_field_id, $entity_type_id);
    $key_map = $this->buildKeyMap($source_field_id, $target_field_id, $entity_type_id, $revision);
    $method = $this->deriveUpdateMethod($target_field_id, $entity_type_id);

    return new FieldMapping($source_table, $target_table, $column_map, $key_map, $method);
  }

  /**
   * Determine the update method for writing to the given field's table.
   *
   * @param string $field_id
   *   An entity field ID.
   * @param string $entity_type_id
   *   An entity type ID.
   *
   * @return string
   *   A string. Either 'update' or 'insert'.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function deriveUpdateMethod($field_id, $entity_type_id) {
    $map = $this->getTableMapping($entity_type_id);
    $field_definition = $this->getFieldStorageDefinition($field_id, $entity_type_id);
    if ($map->requiresDedicatedTableStorage($field_definition)) {
      return FieldMappingInterface::METHOD_INSERT;
    }
    return FieldMappingInterface::METHOD_UPDATE;
  }

  /**
   * Derive a database table name from the given field and entity type ID.
   *
   * @param string $field_id
   *   An entity field ID.
   * @param string $entity_type_id
   *   An entity type ID.
   * @param false $revision
   *   (optional) Whether to retrieve the name for the default or revision
   *   table. Defaults to FALSE.
   *
   * @return string
   *   The name of a database table.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function deriveTableName($field_id, $entity_type_id, $revision = FALSE) {
    $map = $this->getTableMapping($entity_type_id);
    $field_definition = $this->getFieldStorageDefinition($field_id, $entity_type_id);
    if ($map->requiresDedicatedTableStorage($field_definition)) {
      $table_name = $revision
        ? $map->getDedicatedRevisionTableName($field_definition)
        : $map->getDedicatedDataTableName($field_definition);
    }
    elseif ($map->getDataTable()) {
      $table_name = $revision
        ? $map->getRevisionDataTable()
        : $map->getBaseTable();
    }
    else {
      $table_name = $revision
        ? $map->getRevisionTable()
        : $map->getBaseTable();
    }

    return $table_name;
  }

  /**
   * Build a mapping between source and target field table columns.
   *
   * @param string $source_field_id
   *   An entity field ID.
   * @param string $target_field_id
   *   An entity field ID.
   * @param string $entity_type_id
   *   An entity type ID.
   *
   * @return array
   *   An array keyed by columns of the source field database table and
   *   assigning each a column name of the target field database table.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\Sql\SqlContentEntityStorageException
   */
  protected function buildColumnMap(string $source_field_id, string $target_field_id, string $entity_type_id) {
    $map = $this->getTableMapping($entity_type_id);
    $source_field_definition = $this->getFieldStorageDefinition($source_field_id, $entity_type_id);
    $target_field_definition = $this->getFieldStorageDefinition($target_field_id, $entity_type_id);

    $column_map = [];
    $property_names = array_intersect($source_field_definition->getPropertyNames(), array_keys($map->getColumnNames($source_field_id)));
    foreach ($property_names as $property_name) {
      $source_column_name = $map->getFieldColumnName($source_field_definition, $property_name);
      $target_column_name = $map->getFieldColumnName($target_field_definition, $property_name);
      $column_map[$source_column_name] = $target_column_name;
    }

    $source_table = $this->deriveTableName($source_field_id, $entity_type_id);
    $target_table = $this->deriveTableName($target_field_id, $entity_type_id);
    $extra_columns = array_intersect(
      $map->getExtraColumns($source_table),
      $map->getExtraColumns($target_table));
    $extra_columns = array_combine($extra_columns, $extra_columns);

    return array_merge($column_map, $extra_columns);
  }

  protected function buildPropertyMap($field_id, $entity_type_id) {
    $table_mapping = $this->getTableMapping($entity_type_id);
    $field_definition = $this->getFieldStorageDefinition($field_id, $entity_type_id);

    $property_map = [];
    $property_names = array_intersect(
      $field_definition->getPropertyNames(),
      array_keys($table_mapping->getColumnNames($field_id)));
    foreach ($property_names as $property_name) {
      $property_map[$property_name] = $table_mapping
        ->getFieldColumnName($field_definition, $property_name);
    }

    return $property_map;
  }

  /**
   * Build a mapping between source and target field key columns.
   *
   * @param string $source_field_id
   *   An entity field ID.
   * @param string $target_field_id
   *   An entity field ID.
   * @param string $entity_type_id
   *   An entity type ID.
   *
   * @return array
   *   An array keyed by columns of the source field database table and
   *   assigning each a column name of the target field database table.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function buildKeyMap(string $source_field_id, string $target_field_id, string $entity_type_id, bool $revision = FALSE) {
    $source_field_definition = $this->getFieldStorageDefinition($source_field_id, $entity_type_id);
    $target_field_definition = $this->getFieldStorageDefinition($target_field_id, $entity_type_id);

    $source_pk = $this->derivePrimaryKey($source_field_id, $entity_type_id, $revision);
    $target_pk = $this->derivePrimaryKey($target_field_id, $entity_type_id, $revision);

    $key_map = [$source_pk => $target_pk];
    if ($source_field_definition->isMultiple() && $target_field_definition->isMultiple()) {
      $key_map['delta'] = 'delta';
    }

    return $key_map;
  }

  /**
   * Derive a database table column key for the given field and entity type.
   *
   * @param string $field_id
   *   An entity field ID.
   * @param string $entity_type_id
   *   An entity type ID.
   * @param bool $revision
   *   (optional) Whether to derive a key for a revision table.
   *   Defaults to FALSE.
   *
   * @return string
   *   A column name of the given field's database table.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function derivePrimaryKey(string $field_id, string $entity_type_id, bool $revision = FALSE) {
    $map = $this->getTableMapping($entity_type_id);
    $field_definition = $this->getFieldStorageDefinition($field_id, $entity_type_id);

    if ($map->requiresDedicatedTableStorage($field_definition)) {
      return $revision ? 'revision_id' : 'entity_id';
    }

    $entity_type = $this->entityTypeManager()->getDefinition($entity_type_id);
    return $revision
      ? $entity_type->getKey('revision')
      : $entity_type->getKey('id');
  }

  /**
   * Get a table mapping for the given entity type.
   *
   * @param string $entity_type_id
   *   An entity type ID.
   *
   * @return \Drupal\Core\Entity\Sql\DefaultTableMapping
   *   A default table mapping.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getTableMapping(string $entity_type_id) {
    /** @var \Drupal\Core\Entity\Sql\SqlEntityStorageInterface $storage */
    $storage = $this->entityTypeManager()->getStorage($entity_type_id);
    /** @var \Drupal\Core\Entity\Sql\DefaultTableMapping $map */
    $map = $storage->getTableMapping();
    return $map;
  }

  /**
   * Whether the given field is revisionable.
   *
   * @param string $field_id
   *   An entity field ID.
   * @param string $entity_type_id
   *   An entity type ID.
   *
   * @return bool
   *   TRUE if the given field of the given entity type is revisionable.
   *   FALSE otherwise.
   */
  protected function isRevisionable(string $field_id, string $entity_type_id) {
    return $this->getFieldStorageDefinition($field_id, $entity_type_id)
      ->isRevisionable();
  }

  /**
   * Get a field storage definition for the given field and entity type.
   *
   * @param string $field_id
   *   An entity field ID.
   * @param string $entity_type_id
   *   An entity type ID.
   *
   * @return \Drupal\Core\Field\FieldStorageDefinitionInterface
   *   An entity field storage definition.
   */
  protected function getFieldStorageDefinition(string $field_id, string $entity_type_id) {
    return $this
      ->entityFieldManager()
      ->getFieldStorageDefinitions($entity_type_id)[$field_id];
  }


}
