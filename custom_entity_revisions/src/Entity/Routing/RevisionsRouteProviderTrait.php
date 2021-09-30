<?php

namespace Drupal\custom_entity_revisions\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\custom_entity_revisions\Controller\EntityRevisionsListController;
use Symfony\Component\Routing\Route;

/**
 * Provides route building for revisionable entities.
 */
trait RevisionsRouteProviderTrait {

  /**
   * Build a route to list all of an entities revisions.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   A route object. NULL if none could be built.
   */
  protected function getRevisionsRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('revisions')) {
      $route = new Route($entity_type->getLinkTemplate('revisions'));
      $route
        ->addDefaults([
          '_controller' => EntityRevisionsListController::class . '::listing',
          '_title_callback' => EntityRevisionsListController::class . '::getTitle',
        ])
        ->setRequirements([
          '_permission' => "list {$entity_type->id()} revisions",
        ])
        ->setOptions([
          'parameters' => [
            $entity_type->id() => [
              'type' => 'entity:' . $entity_type->id(),
            ],
          ],
        ]);

      return $route;
    }
    return NULL;
  }

}
