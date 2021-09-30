<?php

namespace Drupal\custom_entity_revisions\Entity\Routing;

use Drupal\Core\Entity\Controller\EntityController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\custom_entity_revisions\Controller\EntityRevisionsListController;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides route building for revisionable entities.
 */
trait RevisionsRouteProviderTrait {

  /**
   * Build a route collection containing all entity revision routes.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\RouteCollection
   *   A route object.
   */
  protected function getAllRevisionRoutes(EntityTypeInterface $entity_type) {
    $routes = new RouteCollection();

    if ($revisions_route = $this->getRevisionsRoute($entity_type)) {
      $routes->add("entity.{$entity_type->id()}.revisions", $revisions_route);
    }

    if ($revision_route = $this->getRevisionRoute($entity_type)) {
      $routes->add("entity.{$entity_type->id()}.revision", $revision_route);
    }

    if ($revision_restore_route = $this->getRevisionRestoreRoute($entity_type)) {
      $routes->add("entity.{$entity_type->id()}.revision_restore_form", $revision_restore_route);
    }

    return $routes;
  }

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
          '_title_callback' => EntityRevisionsListController::class . '::listTitle',
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

  /**
   * Build a route to view a single entity revision.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   A route object. NULL if none could be built.
   */
  protected function getRevisionRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('revision')) {
      $route = new Route($entity_type->getLinkTemplate('revision'));
      $route
        ->addDefaults([
          '_controller' => EntityRevisionsListController::class . '::view',
          '_title_callback' => EntityRevisionsListController::class . '::title',
        ])
        ->setRequirements([
          '_permission' => "view {$entity_type->id()} revisions",
        ])
        ->setOptions([
          'parameters' => [
            $entity_type->id() => [
              'type' => 'entity:' . $entity_type->id(),
            ],
            $entity_type->id() . '_revision' => [
              'type' => 'entity_revision:' . $entity_type->id(),
            ]
          ]
        ]);
      return $route;
    }
    return NULL;
  }

  /**
   * Build a route to view a single entity revision.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   A route object. NULL if none could be built.
   */
  protected function getRevisionRestoreRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('revision-restore-form')) {
      $route = new Route($entity_type->getLinkTemplate('revision-restore-form'));
      $route
        ->addDefaults([
          '_form' => 'Drupal\custom_entity_revisions\Form\RevisionRestoreForm',
        ])
        ->setRequirements([
          '_permission' => "restore {$entity_type->id()} revisions",
        ])
        ->setOptions([
          'parameters' => [
            $entity_type->id() => [
              'type' => 'entity:' . $entity_type->id(),
            ],
            $entity_type->id() . '_revision' => [
              'type' => 'entity_revision:' . $entity_type->id(),
            ]
          ]
        ]);
      return $route;
    }
    return NULL;
  }

}
