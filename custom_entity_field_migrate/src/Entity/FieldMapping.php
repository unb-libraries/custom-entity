<?php

namespace Drupal\custom_entity_field_migrate\Entity;

/**
 * Defines a schema for migrating data between two entity fields.
 */
class FieldMapping implements FieldMappingInterface {

  /**
   * The source table name.
   *
   * @var string
   */
  protected $sourceTable;

  /**
   * The target table name.
   *
   * @var string
   */
  protected $targetTable;

  /**
   * The source table key.
   *
   * @var string|array
   */
  protected $sourceKey;

  /**
   * The target table key.
   *
   * @var string|array
   */
  protected $targetKey;

  /**
   * The source-column-to-target-column map.
   *
   * @var array.
   */
  protected $columnMap;

  /**
   * Create a new field mapping instance.
   *
   * @param string $source_table
   *   A database table name.
   * @param string $target_table
   *   A database table name.
   * @param mixed $source_key
   *   One or multiple column name(s) of the given source table.
   * @param mixed $target_key
   *   One or multiple column name(s) of the given target table.
   * @param array $column_map
   *   An associate array assigning column names to other column names.
   */
  public function __construct(string $source_table, string $target_table, mixed $source_key, mixed $target_key, array $column_map) {
    $this->sourceTable = $source_table;
    $this->targetTable = $target_table;
    $this->sourceKey = $source_key;
    $this->targetKey = $target_key;
    $this->columnMap = $column_map;
  }

  /**
   * {@inheritDoc}
   */
  public function getSourceTable() {
    return $this->sourceTable;
  }

  /**
   * {@inheritDoc}
   */
  public function getTargetTable() {
    return $this->targetTable;
  }

  /**
   * {@inheritDoc}
   */
  public function getSourceKey() {
    return $this->sourceKey;
  }

  /**
   * {@inheritDoc}
   */
  public function getTargetKey() {
    return $this->targetKey;
  }

  /**
   * {@inheritDoc}
   */
  public function getColumnMap() {
    return $this->columnMap;
  }


}
