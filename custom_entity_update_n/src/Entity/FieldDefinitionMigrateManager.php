<?php

namespace Drupal\custom_entity_update_n\Entity;

use Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Utility\UpdateException;

/**
 * Migrates entity field definitions.
 */
class FieldDefinitionMigrateManager implements FieldDefinitionMigrateManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The entity definition update manager.
   *
   * @var \Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface
   */
  protected $definitionUpdateManager;

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager instance.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Get the entity field manager.
   *
   * @return \Drupal\Core\Entity\EntityFieldManagerInterface
   *   An entity field manager instance.
   */
  protected function entityFieldManager() {
    return $this->entityFieldManager;
  }

  /**
   * Get the entity definition update manager.
   *
   * @return \Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface
   *   An entity definition update manager instance.
   */
  protected function definitionUpdateManager() {
    return $this->definitionUpdateManager;
  }

  /**
   * Construct a new FieldDefinitionMigrateManager instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity type manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface
   *   An entity field manager.
   * @param \Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface $definition_update_manager
   *   An entity definition update manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager, EntityDefinitionUpdateManagerInterface $definition_update_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->definitionUpdateManager = $definition_update_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function installBaseField(string $field_id, string $entity_type_id, string $bundle = NULL) {
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);

    // This is not entirely correct: original baseFieldDefinition could change by the time schema update is done.
    $entity_class = $entity_type->getClass();
    $field_definition = $entity_class::baseFieldDefinitions($entity_type)[$field_id];
    if (!$field_definition) {
      throw new UpdateException("No field definition {$field_id} found for entity type {$entity_type->getLabel()}");
    }

    $this->definitionUpdateManager()
      ->installFieldStorageDefinition($field_id, $entity_type_id, $entity_type->getProvider(), $field_definition);
  }

  /**
   * {@inheritDoc}
   */
  public function uninstallBaseField(string $field_id, string $entity_type_id) {
    if (!$field_definition = $this->entityFieldManager()->getFieldStorageDefinitions($entity_type_id)[$field_id]) {
      $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
      throw new UpdateException("No field definition {$field_id} found for entity type {$entity_type->getLabel()}");
    }

    $this->definitionUpdateManager()
      ->uninstallFieldStorageDefinition($field_definition);
  }

}
