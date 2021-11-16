<?php

namespace Drupal\custom_entity_events\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Interface for subscribers to entity events.
 *
 * @package Drupal\lib_unb_custom_entity\EventSubscriber
 */
interface EntityEventSubscriberInterface extends EventSubscriberInterface {

  /**
   * Retrieve the entity type the subscriber should process.
   *
   * @return string|false
   *   An entity type ID string. FALSE if the subscriber
   *   should process entity events of any type.s
   */
  public function getEntityTypeId();

}
