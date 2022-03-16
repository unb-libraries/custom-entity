<?php

namespace Drupal\custom_entity_revisions\Entity;
/**
 * Provides access to other revisions of the same entity instance.
 */
trait EntityRevisionsTrait {

  /**
   * {@inheritDoc}
   */
  public function getRevisions() {
    if ($this->getEntityType()->isRevisionable()) {
      $storage = $this->entityTypeManager()->getStorage($this->getEntityTypeId());
      return $storage->loadEntityRevisions($this);
    }
    return [];
  }

  /**
   * {@inheritDoc}
   */
  public function getPreviousRevision() {
    if ($this->getEntityType()->isRevisionable()) {
      $storage = $this->entityTypeManager()->getStorage($this->getEntityTypeId());
      return $storage->loadPreviousRevision($this);
    }
    return NULL;
  }

}
