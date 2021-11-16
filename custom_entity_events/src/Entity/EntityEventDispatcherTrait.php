<?php

namespace Drupal\custom_entity_events\Entity;

use Drupal\custom_entity_events\Event\EntityEvent;
use Drupal\custom_entity_events\Event\EntityEvents;

/**
 * Provides event dispatching for created, updated, deleted entities.
 */
trait EntityEventDispatcherTrait {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $dispatcher;

  /**
   * Get the event dispatcher.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
   *   An event dispatcher object.
   */
  protected function eventDispatcher() {
    return $this->dispatcher;
  }

  /**
   * Dispatch an event of the given type.
   *
   * @param string $event_type
   *   One of @see \Drupal\custom_entity_events\Event\EntityEvents::getEvents()
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that is being saved.
   */
  protected function dispatch($event_type, $entity) {
    if (($dispatcher = $this->eventDispatcher()) && in_array($event_type, EntityEvents::getEvents())) {
      $dispatcher->dispatch(new EntityEvent($entity), $event_type);
    }
  }

}
