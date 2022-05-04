<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Menu\LocalActionDefault;

/**
 * Local action builder for content entities' fields settings.
 */
class LocalEntityFieldsActionBuilder extends LocalContentEntityActionBuilder {

  /**
   * {@inheritDoc}
   */
  public function buildLocalAction(EntityTypeInterface $entity_type, string $bundle = '') {
    $local_action = parent::buildLocalAction($entity_type, $bundle);
    $local_action['class'] = LocalActionDefault::class;
    return $local_action;
  }

  /**
   * {@inheritDoc}
   */
  protected function getId(EntityTypeInterface $entity_type, string $bundle = '') {
    if ($bundle) {
      return "entity.{$entity_type->id()}.{$bundle}.manage_fields";
    }
    return "entity.{$entity_type->id()}.manage_fields";
  }

  /**
   * {@inheritDoc}
   */
  protected function getTitle(EntityTypeInterface $entity_type, string $bundle = '') {
    return $this->t('Manage fields');
  }

  /**
   * {@inheritDoc}
   */
  protected function getTargetRouteId(EntityTypeInterface $entity_type, string $bundle = '') {
    return "entity.{$entity_type->id()}.field_ui_fields";
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
