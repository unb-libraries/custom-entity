<?php

namespace Drupal\custom_entity\FieldSubscriber;

/**
 * Collector for 'field_subscriber' services.
 */
class FieldSubscriberCollector implements FieldSubscriberCollectorInterface {

  /**
   * The collection of field subscribers.
   *
   * @var \Drupal\custom_entity\FieldSubscriber\FieldSubscriberInterface[]
   */
  protected $fieldSubscribers;

  /**
   * {@inheritDoc}
   */
  public function getFieldSubscribers() {
    return $this->fieldSubscribers;
  }

  /**
   * {@inheritDoc}
   */
  public function addFieldSubscriber(FieldSubscriberInterface $field_subscriber) {
    $this->fieldSubscribers[] = $field_subscriber;
  }

}
