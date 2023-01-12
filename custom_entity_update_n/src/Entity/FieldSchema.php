<?php

namespace Drupal\custom_entity_update_n\Entity;

/**
 * Provides access an entity field's underlying database layout.
 */
class FieldSchema implements FieldSchemaInterface {

  /**
   * The database table names.
   *
   * @var array
   */
  protected $tables;

  /**
   * The mapping between field properties and database columns.
   *
   * @var array
   */
  protected $columns;

  /**
   * The database table primary keys.
   *
   * @var array
   */
  protected $keys;

  /**
   * @param string $tables
   *   An array of database table names.
   * @param string $columns
   *   An array of the form FIELD_PROPERTY => DB_COLUMN_NAME.
   * @param array $keys
   *   An array of the form DB_TABLE_NAME => PRIMARY_KEY
   */
  public function __construct(array $tables, array $columns, array $keys) {
    $this->tables = $tables;
    $this->columns = $columns;
    $this->keys = $keys;
  }

  /**
   * {@inheritDoc}
   */
  public function getTables() {
    return $this->tables;
  }

  /**
   * {@inheritDoc}
   */
  public function getPropertyColumnMap() {
    return $this->columns;
  }

  /**
   * {@inheritDoc}
   */
  public function getKey(string $table) {
    return $this->keys[$table];
  }

}
