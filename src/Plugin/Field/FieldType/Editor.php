<?php

namespace Drupal\custom_entity\Plugin\Field\FieldType;

/**
 * Defines the 'editor' entity field type.
 *
 * This builds upon the "entity_reference" field type and always references
 * the "user" entity. Similar to the "changed" field type, the field value will
 * change to the last user who edited the entity. An initial value is assigned
 * upon creation of the entity.
 *
 * @FieldType(
 *   id = "editor",
 *   label = @Translation("Editor"),
 *   description = @Translation("A field referencing the last user who edited an entity."),
 *   category = @Translation("Reference"),
 *   no_ui = TRUE,
 *   cardinality = 1,
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 *   default_formatter = "entity_reference_label"
 * )
 */
class Editor extends Creator {

  /**
   * {@inheritDoc}
   */
  public function preSave() {
    parent::preSave();

    if (!$this->target_id) {
      $this->target_id = $this->currentUser()->id();
    }
    else {
      // On an existing entity translation, the editor will only be
      // set to the current user automatically if at least one other field value
      // of the entity has changed. This detection does not run on new entities
      // and will be turned off if the editor is set manually before
      // save, for example during migrations.
      /** @var \Drupal\Core\Entity\ContentEntityInterface $entity */
      $entity = $this->getEntity();
      /** @var \Drupal\Core\Entity\ContentEntityInterface $original */
      $original = $this->getOriginalEntity();
      $langcode = $entity->language()->getId();
      if (!$entity->isNew() && $original && $original->hasTranslation($langcode)) {
        $original_value = $original->getTranslation($langcode)->get($this->getFieldDefinition()->getName())->target_id;
        if ($this->target_id == $original_value && $entity->hasTranslationChanges()) {
          $this->target_id = $this->currentUser()->id();
        }
      }
    }
  }

  /**
   * The original entity, before it was edited.
   *
   * @return \Drupal\Core\Entity\FieldableEntityInterface
   *   A fieldable entity.
   */
  protected function getOriginalEntity() {
    return $this->getEntity()
      ->original;
  }

}
