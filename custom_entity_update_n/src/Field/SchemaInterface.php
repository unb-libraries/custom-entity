<?php

namespace Drupal\custom_entity_update_n\Field;

/**
 * Interface for field schema objects.
 *
 * A field schema provides insights into the part of the underlying database
 * layout that is relevant to a given field.
 */
interface SchemaInterface {

  /**
   * The database table names.
   *
   * @return array
   *   An array of database table names.
   */
  public function getTables();

  /**
   * A map of field properties and database column names.
   *
   * @return array
   *   An array keyed by field properties, assigning each a database column
   *   name.
   */
  public function getPropertyColumnMap();

  /**
   * Get the name of a database column that acts as key for the given table.
   *
   * @param string $table
   *   A database table name.
   *
   * @return string
   *   A database column name.
   */
  public function getKey(string $table);

}
