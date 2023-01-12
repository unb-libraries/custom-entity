<?php

namespace Drupal\custom_entity_update_n\Field;

/**
 * Interface for entity field mappings.
 */
interface SchemaUpdateMappingInterface {

  const METHOD_UPDATE = 'update';
  const METHOD_INSERT = 'insert';

  /**
   * The name of the table from which to import data.
   *
   * @return string
   *   A database table name.
   */
  public function getSourceTable();

  /**
   * The name of the table to which to export data.
   *
   * @return string
   *   A database table name.
   */
  public function getTargetTable();

  /**
   * A mapping of source table columns to target table columns.
   *
   * @return array
   *   An array assigning each source table column a target table column.
   */
  public function getColumnMap();

  /**
   * A mapping of source to target table keys.
   *
   * @return array
   *   An array assigning each source key column a target key column.
   */
  public function getKeyMap();

  /**
   * The target table write method.
   *
   * @return string
   *   Either 'update' or 'insert'.
   */
  public function getMethod();

  /**
   * Creates and returns an array representation of the field mapping.
   *
   * @return array
   *   An array.
   */
  public function toArray();

}
