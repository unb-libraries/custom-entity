<?php

namespace Drupal\custom_entity_ui\Menu;

/**
 * Collector of services tagged with "local_entity_action_builder".
 */
class LocalEntityActionBuilderCollector implements LocalEntityActionBuilderCollectorInterface {

  /**
   * The collection of local entity action builders.
   *
   * @var
   */
  protected $actionBuilders;

  /**
   * {@inheritDoc}
   */
  public function getLocalEntityActionBuilders() {
    return $this->actionBuilders;
  }

  /**
   * {@inheritDoc}
   */
  public function addLocalEntityActionBuilder(LocalEntityActionBuilderInterface $action_builder) {
    $this->actionBuilders[] = $action_builder;
  }


}
