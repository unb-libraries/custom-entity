<?php

namespace Drupal\custom_entity_events\Event;

/**
 * Defines custom entity specific events.
 */
final class EntityEvents {

  const SAVE = 'entity.save';
  const CREATE = 'entity.create';
  const UPDATE = 'entity.update';
  const DELETE = 'entity.delete';

  /**
   * Retrieve all events defined by this class.
   *
   * @return string[]
   *   An array of event type identifiers.
   */
  public static function getEvents() {
    return [
      self::SAVE,
      self::CREATE,
      self::UPDATE,
      self::DELETE,
    ];
  }

}
