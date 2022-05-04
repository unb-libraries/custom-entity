<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Local action builder for content entities' form display settings.
 */
class LocalEntityFormDisplayActionBuilder extends LocalContentEntityActionBuilder {

  /**
   * {@inheritDoc}
   */
  protected function getId(EntityTypeInterface $entity_type, string $bundle = '') {
    if ($bundle) {
      return "entity.{$entity_type->id()}.{$bundle}.anage_form";
    }
    return "entity.{$entity_type->id()}.manage_form";
  }

  /**
   * {@inheritDoc}
   */
  protected function getTitle(EntityTypeInterface $entity_type, string $bundle = '') {
    return $this->t('Manage form display');
  }

  /**
   * {@inheritDoc}
   */
  protected function getTargetRouteId(EntityTypeInterface $entity_type, string $bundle = '') {
    return "entity.entity_form_display.{$entity_type->id()}.default";
  }

  /**
   * {@inheritDoc}
   */
  protected function getAppearsOn(EntityTypeInterface $entity_type, string $bundle = '') {
    $appears_on = [];

    if ($pattern = $entity_type->getLinkTemplate('add-form')) {
      $routes = $this->routeProvider()->getRoutesByPattern($pattern)->all();
      $appears_on = array_merge($appears_on, array_keys($routes));
    }

    if ($pattern = $entity_type->getLinkTemplate('edit-form')) {
      $routes = $this->routeProvider()->getRoutesByPattern($pattern)->all();
      $appears_on = array_merge($appears_on, array_keys($routes));
    }

    return $appears_on;
  }
}
