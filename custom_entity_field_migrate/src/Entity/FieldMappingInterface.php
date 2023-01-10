<?php

namespace Drupal\custom_entity_field_migrate\Entity;

/**
 * Interface for entity field mappings.
 */
interface FieldMappingInterface {

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
   * The table column(s) to uniquely identify data inside the source table.
   *
   * @return string|array
   *   One or more database column names.
   */
  public function getSourceKey();

  /**
   * The table column(s) to uniquely identify data inside the target table.
   *
   * @return string|array
   *   One or more database column names.
   */
  public function getTargetKey();

  /**
   * A mapping of source table columns to target table columns.
   *
   * @return array
   *   An array assigning each source table column a target table column.
   */
  public function getColumnMap();

}
