<?php

namespace Drupal\custom_entity_field_migrate\Entity;

/**
 * Interface for FieldMapping creators.
 */
interface FieldMappingFactoryInterface {

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
   * @return \Drupal\custom_entity_field_migrate\Entity\FieldMappingInterface
   *   A field mapping instance.
   */
  public function create(string $source_field_id, string $target_field_id, string $entity_type_id, string $bundle = NULL);

}