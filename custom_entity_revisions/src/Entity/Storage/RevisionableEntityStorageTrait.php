<?php

namespace Drupal\custom_entity_revisions\Entity\Storage;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableInterface;

/**
 * Trait to enhance Drupal's default storage handler for revisionable entities.
 */
trait RevisionableEntityStorageTrait {

  /**
   * Get the database connection.
   *
   * @return \Drupal\Core\Database\Connection
   *   A connection object.
   */
  protected function database() {
    return $this->database;
  }

  /**
   * {@inheritDoc}
   */
  public function loadEntityRevisions(EntityInterface $entity) {
    /** @var \Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageInterface $this */

    $revision_id_key = $this->getEntityType()->getKey('revision');
    $revision_table = $this->getEntityType()->getRevisionTable();

    $revision_ids = $this->database()->query(
      "SELECT {$revision_id_key} FROM {$revision_table} WHERE id = :entity_id", [
        ':entity_id' => $entity->id(),
      ]
    )->fetchCol();

    return $this->loadMultipleRevisions($revision_ids);
  }

  /**
   * {@inheritDoc}
   */
  public function loadPreviousRevision(RevisionableInterface $entity_revision) {
    /** @var \Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageInterface $this */

    $entity_revision_key = $this->getEntityType()->getKey('revision');
    $revision_table = $this->getEntityType()->getRevisionTable();

    $params = [
      ':entity_id' => $entity_revision->id(),
      ':entity_revision_id' => $entity_revision->getRevisionId(),
      ':entity_revision_key' => $entity_revision_key,
    ];

    $query = str_replace(array_keys($params), array_values($params), "
        SELECT {$entity_revision_key}
        FROM {$revision_table}
        WHERE id = {$entity_revision->id()}
        AND {$entity_revision_key} <> {$entity_revision->getRevisionId()}
        ORDER BY {$entity_revision_key} DESC
        LIMIT 1");

    $revision_id = $this->database()->query($query)->fetchCol();
    var_dump($revision_id);
    if (!empty($revision_id)) {
      return $this->loadRevision($revision_id[0]);
    }
    return FALSE;
  }

}
