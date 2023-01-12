<?php

namespace Drupal\custom_entity_update_n\Field;

/**
 * Interface for SchemaUpdateMapping creators.
 */
interface SchemaUpdateMappingFactoryInterface {

  /**
   * Create a field schema.
   *
   * @param string $field_id
   *   An entity field ID.
   * @param string $entity_type_id
   *   An entity type ID.
   * @param string|null $bundle
   *   (optional) An entity bundle.
   *
   * @return \Drupal\custom_entity_update_n\Field\SchemaInterface
   *   A field schema object.
   */
  public function createSchema(string $field_id, string $entity_type_id, string $bundle = NULL);

  /**
   * Creates a field mapping between source and target field of the given entity and bundle.
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
   * @return \Drupal\custom_entity_update_n\Field\SchemaUpdateMappingInterface[]
   *   A field mapping instance.
   */
  public function create(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL);

}
