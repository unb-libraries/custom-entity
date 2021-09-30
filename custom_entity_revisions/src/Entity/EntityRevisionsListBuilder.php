<?php

namespace Drupal\custom_entity_revisions\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Url;

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

    // @todo Add class to highlight default revision.
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

    if (!$revision->isDefaultRevision()) {
      if ($account->hasPermission("view {$revision->getEntityTypeId()} revisions") && $revision->hasLinkTemplate('revision')) {
        $operations['view'] = [
          'title' => $this->t('View'),
          'weight' => 10,
          'url' => $revision->toUrl('revision', [
            'schedule_revision' => $revision,
          ]),
        ];
      }

      if ($account->hasPermission("restore {$revision->getEntityTypeId()} revisions") && $revision->hasLinkTemplate('revision-restore-form')) {
        $operations['restore'] = [
          'title' => $this->t('Restore'),
          'weight' => 20,
          'url' => $this->ensureDestination(Url::fromRoute("entity.{$revision->getEntityTypeId()}.revision_restore_form", [
            $revision->getEntityTypeId() => $revision->id(),
            $revision->getEntityTypeId() . '_revision' => $revision->getRevisionId(),
          ])),
        ];
      }
    }

    return $operations;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    $table = parent::render();

    // @todo Refactor the render method.
    $table['table']['#rows'] = [];
    foreach ($this->load() as $revision) {
      if ($row = $this->buildRow($revision)) {
        $table['table']['#rows'][$revision->getRevisionId()] = $row;
      }
    }

    return $table;
  }

}
