<?php

namespace Drupal\custom_entity_update_n\Entity;

interface SchemaUpdateManagerInterface {

  /**
   * Install an entity field.
   *
   * @param string $field_id
   *   The ID of the field to install.
   * @param string $entity_type_id
   *   The ID of the field's entity type.
   * @param string|null $bundle
   *   The field's bundle.
   *
   * @throws \Drupal\Core\Utility\UpdateException
   */
  public function installBaseField(string $field_id, string $entity_type_id, string $bundle = NULL);

  /**
   * Uninstall an entity field.
   *
   * @param string $field_id
   *   The ID of the field to install.
   * @param string $entity_type_id
   *   The ID of the field's entity type.
   *
   * @throws \Drupal\Core\Utility\UpdateException
   */
  public function uninstallBaseField(string $field_id, string $entity_type_id);

}
