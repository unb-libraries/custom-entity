<?php

namespace Drupal\custom_entity_revisions\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Builds instances of entity revision list builders.
 *
 * @package Drupal\custom_entity_revisions\Entity
 */
class EntityRevisionsListBuilderFactory implements EntityRevisionsListBuilderFactoryInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager object.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Construct an EntityRevisionsListBuilderFactory instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function createInstance(EntityInterface $entity) {
    $storage = $this
      ->entityTypeManager()
      ->getStorage($entity->getEntityTypeId());
    return new EntityRevisionsListBuilder($entity, $storage);
  }

}
