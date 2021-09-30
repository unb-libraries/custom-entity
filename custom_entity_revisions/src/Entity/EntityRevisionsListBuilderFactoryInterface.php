<?php

namespace Drupal\custom_entity_revisions\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for entity revisions list builder factories.
 *
 * @package Drupal\custom_entity_revisions\Entity
 */
interface EntityRevisionsListBuilderFactoryInterface {

  /**
   * Create an entity revisions list builder instance.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return \Drupal\Core\Entity\EntityListBuilderInterface
   *   A list builder object.
   */
  public function createInstance(EntityInterface $entity);

}
