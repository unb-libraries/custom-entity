<?php

namespace Drupal\custom_entity_events\Event;

use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Defines a base class for all entity type events.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Event
 */
class EntityEvent extends GenericEvent {

  /**
   * Create a new EntityEvent instance.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity object.
   */
  public function __construct(EntityInterface $entity) {
    parent::__construct($entity, []);
  }

  /**
   * Retrieve the entity, i.e. the subject of the event.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   An entity object.
   */
  public function getEntity() {
    return $this->getSubject();
  }

}
