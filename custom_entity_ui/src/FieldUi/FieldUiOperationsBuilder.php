<?php

namespace Drupal\custom_entity_ui\FieldUi;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Url;

/**
 * Builds operations links to FieldUI routes.
 *
 * @package Drupal\custom_entity_ui\Controller
 */
trait FieldUiOperationsBuilder {

  /**
   * Get all FieldUI operations links for the given entity type and bundle.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   * @param \Drupal\Core\Config\Entity\ConfigEntityInterface|null $bundle
   *   (optional) An instance of a config entity that is a bundle of the entity
   *   type. If none is given, it is assumed that the entity type does not have
   *   bundles.
   *
   * @return array
   *   A renderable operations links array.
   */
  protected function getFieldUiOperations(EntityTypeInterface $entity_type, ConfigEntityInterface $bundle = NULL) {
    $operations = [];

    if (!$field_ui_base_route_name = $entity_type->get('field_ui_base_route')) {
      return $operations;
    }

    if ($bundle) {
      $operations["edit-{$bundle->id()}"] = [
        'title' => $this->t('Edit @bundle', [
          '@bundle' => $bundle->label(),
        ]),
        'weight' => 10,
        'url' => Url::fromRoute($field_ui_base_route_name, [
          $bundle->getEntityType()->id() => $bundle->id(),
        ]),
      ];
    }
    else {
      $operations['edit'] = [
        'title' => $this->t('Edit @entity_type', [
          '@entity_type' => $entity_type->getLabel(),
        ]),
        'weight' => 10,
        'url' => Url::fromRoute($field_ui_base_route_name),
      ];
    }

    return $operations;
  }

}
