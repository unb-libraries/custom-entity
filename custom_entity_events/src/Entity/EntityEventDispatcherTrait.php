<?php

namespace Drupal\custom_entity_events\Entity;

use Drupal\Core\Entity\EntityInterface;
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
   * Dispatch events related to saving  the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that has been or will be saved.
   * @param bool $is_new
   *   Whether the given entity is (or has been, before saving it) new.
   */
  protected function dispatchSave(EntityInterface $entity, bool $is_new) {
    $this->dispatch(EntityEvents::SAVE, $entity);
    if ($is_new) {
      $this->dispatch(EntityEvents::CREATE, $entity);
    }
    else {
      $this->dispatch(EntityEvents::UPDATE, $entity);
    }
  }

  /**
   * Dispatch an event of the given type.
   *
   * @param string $event_type
   *   One of @see \Drupal\custom_entity_events\Event\EntityEvents::getEvents()
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that is being saved.
   */
  protected function dispatch(string $event_type, EntityInterface $entity) {
    if (($dispatcher = $this->eventDispatcher()) && in_array($event_type, EntityEvents::getEvents())) {
      $dispatcher->dispatch(new EntityEvent($entity), $event_type);
    }
  }

}
