<?php

namespace Drupal\custom_entity_examples\EventSubscriber;

use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\custom_entity_events\Event\EntityEvent;
use Drupal\custom_entity_events\EventSubscriber\EntityEventSubscriber;

class BlogEventSubscriber extends EntityEventSubscriber {

  use MessengerTrait;
  use StringTranslationTrait;

  /**
   * {@inheritDoc}
   */
  public function doOnEntityCreate(EntityEvent $event) {
    $this->messenger()->addStatus($this->t('An event was fired upon the creation of "@blog".', [
      '@blog' => $event->getEntity()->label(),
    ]));
  }
}
