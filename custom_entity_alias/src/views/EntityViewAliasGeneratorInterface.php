<?php

namespace Drupal\custom_entity_alias\views;

use Drupal\Core\Entity\EntityInterface;
use Drupal\views\ViewEntityInterface;

/**
 * Interface for EntityViewAliasGenerators.
 */
interface EntityViewAliasGeneratorInterface {

  /**
   * Generate a path alias for the given view and entity.
   *
   * @param \Drupal\views\ViewEntityInterface $view
   *   The view.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param array $options
   *   (optional) An array of options.
   *   @see \Drupal\ior\views\EntityViewAliasGenerator::resolveContextualPath()
   */
  public function generateViewAlias(ViewEntityInterface $view, EntityInterface $entity, array $options = []);

}
