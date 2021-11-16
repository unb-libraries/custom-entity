<?php

namespace Drupal\custom_entity_events\EventSubscriber;

use Drupal\custom_entity_events\Event\EntityEvent;
use Drupal\custom_entity_events\Event\EntityEvents;

/**
 * Base class for entity event subscriber implementations.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\EventSubscriber
 */
abstract class EntityEventSubscriber implements EntityEventSubscriberInterface {

  /**
   * The entity type the subscriber should process.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * {@inheritDoc}
   */
  public function getEntityTypeId() {
    if (isset($this->entityTypeId)) {
      return $this->entityTypeId;
    }
    return FALSE;
  }

  /**
   * Create a new entity event subscriber instance.
   *
   * @param string|false $entity_type_id
   *   An entity type ID.
   */
  public function __construct($entity_type_id = FALSE) {
    if ($entity_type_id) {
      $this->entityTypeId = $entity_type_id;
    }
  }

  /**
   * {@inheritDoc}
   */
  final public static function getSubscribedEvents() {
    $event_names = static::getSubscribedEventNames();
    return array_map(function ($event_type) {
      $parts = array_map(function ($part) {
        return ucfirst(strtolower($part));
      }, explode('.', $event_type));
      return 'on' . implode('', $parts);
    }, array_combine($event_names, $event_names));
  }

  /**
   * Returns an array of event names this subscriber wants to listen to.
   *
   * This returns only the event names, while getSubscribedEvents maps to
   * callable handler methods.
   *
   * @return array
   *   An array of event names.
   */
  protected static function getSubscribedEventNames() {
    return [
      EntityEvents::SAVE,
      EntityEvents::CREATE,
      EntityEvents::UPDATE,
      EntityEvents::DELETE,
    ];
  }

  /**
   * Whether the subscriber handles the passed event.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   The event.
   *
   * @return bool
   *   TRUE if the passed event was triggered by an entity of
   *   a type to which the handler subscribes. FALSE otherwise.
   */
  public function doesHandle(EntityEvent $event) {
    return !$this->getEntityTypeId()
      || $this->getEntityTypeId() === $event->getEntity()->getEntityTypeId();
  }

  /**
   * Process an entity.SAVE event.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   An entity event object.
   */
  final public function onEntitySave(EntityEvent $event) {
    if ($this->doesHandle($event)) {
      $this->doOnEntitySave($event);
    }
  }

  /**
   * The actual processing of an entity.SAVE event.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   An entity event object.
   */
  public function doOnEntitySave(EntityEvent $event) {}

  /**
   * Process an entity.CREATE event.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   An entity event object.
   */
  final public function onEntityCreate(EntityEvent $event) {
    if ($this->doesHandle($event)) {
      $this->doOnEntityCreate($event);
    }
  }

  /**
   * The actual processing of an entity.CREATE event.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   An entity event object.
   */
  public function doOnEntityCreate(EntityEvent $event) {}

  /**
   * Process an entity.UPDATE event.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   An entity event object.
   */
  final public function onEntityUpdate(EntityEvent $event) {
    if ($this->doesHandle($event)) {
      $this->doOnEntityUpdate($event);
    }
  }

  /**
   * The actual processing of an entity.UPDATE event.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   An entity event object.
   */
  public function doOnEntityUpdate(EntityEvent $event) {}

  /**
   * Process an entity.DELETE event.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   The entity event object.
   */
  final public function onEntityDelete(EntityEvent $event) {
    if ($this->doesHandle($event)) {
      $this->doOnEntityDelete($event);
    }
  }

  /**
   * The actual processing of an entity.DELETE event.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   An entity event object.
   */
  public function doOnEntityDelete(EntityEvent $event) {}

}
