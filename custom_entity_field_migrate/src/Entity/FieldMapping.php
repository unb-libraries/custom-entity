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
   * The source-column-to-target-column map.
   *
   * @var array
   */
  protected $columnMap;

  /**
   * The source-key-to-target-key map.
   *
   * @var array
   */
  protected $keyMap;

  /**
   * Create a new field mapping instance.
   *
   * @param string $source_table
   *   A database table name.
   * @param string $target_table
   *   A database table name.
   * @param array $column_map
   *   An associate array assigning column names to other column names.
   * @param array $key_map
   *   An associate array assigning key column names to other key column names.
   */
  public function __construct(string $source_table, string $target_table, array $column_map, array $key_map) {
    $this->sourceTable = $source_table;
    $this->targetTable = $target_table;
    $this->columnMap = $column_map;
    $this->keyMap = $key_map;
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
  public function getColumnMap() {
    return $this->columnMap;
  }

  /**
   * {@inheritDoc}
   */
  public function getKeyMap() {
    return $this->keyMap;
  }

  /**
   * {@inheritDoc}
   */
  public function toArray() {
    return [
      'tables' => [
        $this->getSourceTable() => $this->getTargetTable(),
      ],
      'keys' => $this->getKeyMap(),
      'columns' => $this->getColumnMap(),
    ];
  }


}
