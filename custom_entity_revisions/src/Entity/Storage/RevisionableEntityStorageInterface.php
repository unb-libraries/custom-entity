<?php

namespace Drupal\custom_entity_revisions\Entity\Storage;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableStorageInterface;
use Drupal\Core\Entity\RevisionableInterface;

/**
 * Storage handler interface for revisionable entities.
 *
 * @package Drupal\lib_unb_custom_entity\Entity\Storage
 */
interface RevisionableEntityStorageInterface extends RevisionableStorageInterface {

  /**
   * Load all revisions of the given entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   *
   * @return \Drupal\Core\Entity\RevisionableInterface[]
   *   An array of entity revisions.
   */
  public function loadEntityRevisions(EntityInterface $entity);

  /**
   * Load the revision that preceded the one given.
   *
   * @param \Drupal\Core\Entity\RevisionableInterface $entity_revision
   *   A revision of an entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|false
   *   A revision of an entity. FALSE if there is no previous revision, i.e.
   *   if the given entity is the first revision.
   */
  public function loadPreviousRevision(RevisionableInterface $entity_revision);

}
