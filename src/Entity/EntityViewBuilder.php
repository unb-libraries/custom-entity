<?php

namespace Drupal\custom_entity\Entity;

use Drupal\Core\Entity\EntityViewBuilder as DefaultEntityViewBuilder;
use Drupal\Core\Messenger\MessengerTrait;

/**
 * Enhances Drupal's default EntityViewBuilder.
 */
class EntityViewBuilder extends DefaultEntityViewBuilder {

  use MessengerTrait;

  /**
   * Retrieve the entity type ID.
   *
   * @return string
   *   A string.
   */
  protected function getEntityTypeId() {
    return $this->getEntityType()->id();
  }

  /**
   * Retrieve the entity type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type object.
   */
  protected function getEntityType() {
    return $this->entityType;
  }

}
