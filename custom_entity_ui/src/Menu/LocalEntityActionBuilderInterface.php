<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Interface for local entity action builders.
 */
interface LocalEntityActionBuilderInterface {

  /**
   * Whether this builder builds local actions for the given entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   * @param string $bundle
   *   (optional) A bundle, if the entity type supports bundles.
   *
   * @return bool
   *   TRUE if the builder can produce local actions for the given entity
   *   type. FALSE otherwise.
   */
  public function applies(EntityTypeInterface $entity_type, string $bundle = '');

  /**
   * Build a local action for the given entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   * @param string $bundle
   *   (optional) A bundle, if the entity type supports bundles.
   *
   * @return array
   *   A local action definition array.
   */
  public function buildLocalAction(EntityTypeInterface $entity_type, string $bundle = '');

}
