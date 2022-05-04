<?php

namespace Drupal\custom_entity_ui\Controller;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\custom_entity_ui\FieldUi\FieldUiOperationsBuilder;

/**
 * Renders a table of entity types.
 *
 * @package Drupal\custom_entity_ui\Controller
 */
class EntityTypeTableController extends EntityTypeListController {

  use FieldUiOperationsBuilder;

  /**
   * Creates a table of entity types.
   *
   * @return array
   *   A render array structure.
   */
  public function listing() {
    $entity_types = $this->loadEntityTypes();
    return [
      '#type' => 'table',
      '#header' => $this->buildHeader(),
      '#rows' => $this->buildRows($entity_types),
      '#empty' => $this->emptyMessage(),
    ];
  }

  /**
   * Builds the header row for the entity type listing.
   *
   * @return array
   *   A render array structure of header strings.
   *
   * @see \Drupal\custom_entity_ui\Controller\EntityTypeListController::listing()
   */
  protected function buildHeader() {
    return [
      'machine_name' => $this->t('Machine name'),
      'name' => $this->t('Name'),
      'provider' => $this->t('Defined by'),
      'operations' => $this->t('Operations'),
    ];
  }

  /**
   * Builds a row for each entity type in the entity type listing.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface[] $entity_types
   *   An array of entity types.
   *
   * @return array
   *   A render array structure of properties for each entity type.
   *
   * @see \Drupal\custom_entity_ui\Controller\EntityTypeListController::listing()
   */
  protected function buildRows(array $entity_types) {
    $rows = [];
    foreach ($entity_types as $entity_type) {
      $rows[] = $this->buildRow($entity_type);
    }
    return $rows;
  }

  /**
   * Builds a row for an entity type in the entity type listing.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type for this row of the list.
   *
   * @return array
   *   A render array structure of properties for the given entity type.
   */
  protected function buildRow(EntityTypeInterface $entity_type) {
    return [
      'id' => $entity_type->id(),
      'name' => $entity_type->getLabel(),
      'provider' => $entity_type->getProvider(),
      'operations' => [
        'data' => $this->buildOperations($entity_type),
      ],
    ];
  }

  /**
   * The message to display when the list contains no entries.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   A (translatable) string.
   */
  protected function emptyMessage() {
    return $this->t('No custom entity types have been defined.');
  }

  /**
   * Builds a renderable list of operations for the entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type on which the operations will be performed.
   *
   * @return array
   *   A renderable array of operation links.
   *
   * @see \Drupal\custom_entity_ui\Controller\EntityTypeTableController::buildRows()
   */
  protected function buildOperations(EntityTypeInterface $entity_type) {
    return [
      '#type' => 'operations',
      '#links' => $this->getOperations($entity_type),
    ];
  }

  /**
   * Builds a renderable list of operation links for the entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to which the links will point.
   *
   * @return array
   *   A renderable array of links.
   */
  protected function getOperations(EntityTypeInterface $entity_type) {
    $operations = [];

    if ($this->isBundled($entity_type) && !empty($bundles = $this->getBundles($entity_type))) {
      foreach ($bundles as $bundle) {
        $operations += $this->getFieldUiOperations($entity_type, $bundle);
      }
    }
    elseif (!$this->isBundled($entity_type)) {
      $operations += $this->getFieldUiOperations($entity_type);
    }

    return $operations;
  }

}
