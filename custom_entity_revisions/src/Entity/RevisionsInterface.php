<?php

namespace Drupal\custom_entity_revisions\Entity;

/**
 * Interface addon for revisionable entities.
 */
interface RevisionsInterface {

  /**
   * Retrieve all revisions.
   *
   * @return static[]
   *   An array of entity revisions, keyed by each entity's revision ID.
   */
  public function getRevisions();

  /**
   * Retrieve the revision preceding this one.
   *
   * @return static|null
   *   A revision of an entity. NULL if there is no previous revision, i.e.
   *   if this entity is the only one or is not revisionable.
   */
  public function getPreviousRevision();

}
