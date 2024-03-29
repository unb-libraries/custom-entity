<?php

namespace Drupal\custom_entity_revisions\Entity;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\custom_entity\Entity\UserEditedInterface;

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
    /** @var \Drupal\Core\Entity\ContentEntityTypeInterface $entity_type */
    $entity_type = $this->getEntity()->getEntityType();

    $header = [
      $this->entityType->getKey('revision') => $this->t('Revision ID'),
    ];

    if ($entity_type->hasRevisionMetadataKey('revision_created')) {
      $header[$entity_type->getRevisionMetadataKey('revision_created')] = $this->t('Edited');
    }
    else if (is_a($this->getEntity(), UserEditedInterface::class) && $this->getEntity()->hasField(UserEditedInterface::FIELD_EDITED)) {
      $header[UserEditedInterface::FIELD_EDITED] = $this->t('Edited');
    }

    if ($entity_type->hasRevisionMetadataKey('revision_user')) {
      $header[$entity_type->getRevisionMetadataKey('revision_user')] = $this->t('Editor');
    }
    else if (is_a($this->getEntity(), UserEditedInterface::FIELD_EDITOR) && $this->getEntity()->hasField(UserEditedInterface::FIELD_EDITOR)) {
      $header[UserEditedInterface::FIELD_EDITOR] = $this->t('Editor');
    }

    if ($entity_type->hasRevisionMetadataKey('revision_log_message')) {
      $header[$entity_type->getRevisionMetadataKey('revision_log_message')] = $this->t('Message');
    }

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritDoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\Core\Entity\RevisionableInterface $revision */
    $revision = $entity;

    /** @var \Drupal\Core\Entity\ContentEntityTypeInterface $entity_type */
    $entity_type = $revision->getEntityType();

    // @todo Add class to highlight default revision.
    $row = [
      $entity_type->getKey('revision') => $revision->getRevisionId(),
    ];

    if ($entity_type->hasRevisionMetadataKey('revision_created')) {
      $revision_created_key = $entity_type->getRevisionMetadataKey(('revision_created'));
      if ($revision_created = $revision->get($revision_created_key)->value) {
        $row[$revision_created_key] = DrupalDateTime::createFromTimestamp($revision_created)
          ->format('F j, Y g:ia');
      }
    }
    elseif (is_a($revision, UserEditedInterface::class) && $revision->hasField(UserEditedInterface::FIELD_EDITED)) {
      $row[UserEditedInterface::FIELD_EDITED] = DrupalDateTime::createFromTimestamp($revision->getEditedTime())->format('Y-m-d H:i');
    }

    if ($entity_type->hasRevisionMetadataKey('revision_user')) {
      $editor_key = $entity_type->getRevisionMetadataKey(('revision_user'));
      /** @var \Drupal\user\UserInterface $editor */
      if ($editor = $revision->get($editor_key)->entity) {
        $row[$editor_key] = $editor->getDisplayName();
      }
    }
    else if (is_a($revision, UserEditedInterface::class && $revision->hasField(UserEditedInterface::FIELD_EDITOR))) {
      $editor = $revision->getEditor();
      $row[UserEditedInterface::FIELD_EDITOR] = Link::fromTextAndUrl($editor->label(), $editor->toUrl());
    }

    if ($entity_type->hasRevisionMetadataKey('revision_log_message')) {
      $revision_log_key = $entity_type->getRevisionMetadataKey(('revision_log_message'));
      if ($revision_log = $revision->get($revision_log_key)->value) {
        $row[$revision_log_key] = $revision_log;
      }
    }

    return $row + parent::buildRow($entity);
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
