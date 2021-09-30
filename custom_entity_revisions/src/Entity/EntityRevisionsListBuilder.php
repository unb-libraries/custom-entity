<?php

namespace Drupal\custom_entity_revisions\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Builds a list of entity revisions.
 */
class EntityRevisionsListBuilder extends EntityListBuilder {

  protected $entity;

  public function __construct(EntityInterface $entity, EntityStorageInterface $storage) {
    parent::__construct($entity->getEntityType(), $storage);
    $this->entity = $entity;
  }

  /**
   * {@inheritDoc}
   */
  public function buildHeader() {
    return [
      'rid' => $this->t('Revision ID'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritDoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\Core\Entity\RevisionableInterface $revision */
    $revision = $entity;

    return [
      'id' => $revision->getRevisionId(),
    ] + parent::buildRow($entity);
  }

}
