<?php

namespace Drupal\custom_entity_update_n\Entity;

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
   * The method.
   *
   * @var string
   */
  protected $method;

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
   * @param string $method
   *   A string. Either 'update' or 'insert'.
   */
  public function __construct(string $source_table, string $target_table, array $column_map, array $key_map, string $method) {
    $this->sourceTable = $source_table;
    $this->targetTable = $target_table;
    $this->columnMap = $column_map;
    $this->keyMap = $key_map;
    $this->method = in_array($method, [FieldMappingInterface::METHOD_INSERT, FieldMappingInterface::METHOD_UPDATE])
      ? $method
      : FieldMappingInterface::METHOD_UPDATE;
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
  public function getMethod() {
    return $this->method;
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
      'method' => $this->getMethod(),
    ];
  }


}
