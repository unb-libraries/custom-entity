<?php

namespace Drupal\custom_entity_ui\Menu;

/**
 * Interface for local entity action builder collectors.
 */
interface LocalEntityActionBuilderCollectorInterface {

  /**
   * Get all local entity action builders.
   *
   * @return \Drupal\custom_entity_ui\Menu\LocalEntityActionBuilderInterface[]
   *   An array of local entity action builders.
   */
  public function getLocalEntityActionBuilders();

  /**
   * Add a local entity action builder.
   *
   * @param \Drupal\custom_entity_ui\Menu\LocalEntityActionBuilderInterface $action_builder
   *   A local entity action builder.
   */
  public function addLocalEntityActionBuilder(LocalEntityActionBuilderInterface $action_builder);

}
