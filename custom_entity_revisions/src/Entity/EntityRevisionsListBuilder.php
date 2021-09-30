<?php

namespace Drupal\custom_entity_revisions\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Builds a list of entity revisions.
 */
class EntityRevisionsListBuilder extends EntityListBuilder {

  /**
   * The entity, i.e. the current revision.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * Get the entity, i.e. the current revision.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   An entity.
   */
  protected function getEntity() {
    return $this->entity;
  }

  /**
   * Construct an EntityRevisionsListBuilder instance.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity, i.e. the current revision.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   A suitable storage handler for the entity type.
   */
  public function __construct(EntityInterface $entity, EntityStorageInterface $storage) {
    parent::__construct($entity->getEntityType(), $storage);
    $this->entity = $entity;
  }

  /**
   * {@inheritDoc}
   */
  public function load() {
    /** @var \Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageInterface $storage */
    $storage = $this->getStorage();
    $revisions = $storage->loadEntityRevisions($this->getEntity());
    return $revisions;
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
      'rid' => $revision->getRevisionId(),
    ] + parent::buildRow($entity);
  }

  /**
   * {@inheritDoc}
   */
  protected function getDefaultOperations(EntityInterface $entity) {
    /** @var \Drupal\Core\Entity\RevisionableInterface $revision */
    $revision = $entity;

    // @todo Refactor revision access checking.
    $account = \Drupal::currentUser();
    $operations = [];

    if ($account->hasPermission("view {$revision->getEntityTypeId()} revisions") && $revision->hasLinkTemplate('revision')) {
      $operations['view'] = [
        'title' => $this->t('View'),
        'weight' => 10,
        'url' => $this->ensureDestination($revision->toUrl('revision')),
      ];
    }

    return $operations;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    $table = parent::render();

    // @todo Refactor the render method.
    foreach ($this->load() as $revision) {
      if ($row = $this->buildRow($revision)) {
        $table['table']['#rows'][$revision->getRevisionId()] = $row;
      }
    }

    return $table;
  }

}
