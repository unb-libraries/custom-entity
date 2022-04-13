<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Local action builder for content entities' view display settings.
 */
class LocalEntityViewDisplayActionBuilder extends LocalContentEntityActionBuilder {

  /**
   * {@inheritDoc}
   */
  protected function getId(EntityTypeInterface $entity_type, string $bundle = '') {
    return "entity.{$entity_type->id()}.manage_display";
  }

  /**
   * {@inheritDoc}
   */
  protected function getTitle(EntityTypeInterface $entity_type, string $bundle = '') {
    return $this->t('Manage view display');
  }

  /**
   * {@inheritDoc}
   */
  protected function getTargetRouteId(EntityTypeInterface $entity_type, string $bundle = '') {
  return "entity.entity_view_display.{$entity_type->id()}.default";
}

  /**
   * {@inheritDoc}
   */
  protected function getAppearsOn(EntityTypeInterface $entity_type, string $bundle = '') {
    $appears_on = [];

    if ($pattern = $entity_type->getLinkTemplate('canonical')) {
      $routes = $this->routeProvider()->getRoutesByPattern($pattern)->all();
      $appears_on = array_merge($appears_on, array_keys($routes));
    }

    return $appears_on;
}


}
