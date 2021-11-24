<?php

namespace Drupal\custom_entity\FieldSubscriber;

/**
 * Interface for 'field_subscriber' service collectors.
 */
interface FieldSubscriberCollectorInterface {

  /**
   * Add the given field subscriber to the collection.
   *
   * @param \Drupal\custom_entity\FieldSubscriber\FieldSubscriberInterface $field_subscriber
   *   A field subscriber object.
   */
  public function addFieldSubscriber(FieldSubscriberInterface $field_subscriber);

  /**
   * Get all field subscribers.
   *
   * @return \Drupal\custom_entity\FieldSubscriber\FieldSubscriberInterface[]
   *   An array of field subscribers.
   */
  public function getFieldSubscribers();

}
