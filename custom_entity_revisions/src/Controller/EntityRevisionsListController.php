<?php

namespace Drupal\custom_entity_revisions\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\custom_entity_revisions\Entity\EntityRevisionsListBuilderFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller to respond entity revision routes.
 */
class EntityRevisionsListController extends ControllerBase {

  /**
   * The list builder factory.
   *
   * @var \Drupal\custom_entity_revisions\Entity\EntityRevisionsListBuilderFactoryInterface
   */
  protected $listBuilderFactory;

  /**
   * Get the list builder factory.
   *
   * @return \Drupal\custom_entity_revisions\Entity\EntityRevisionsListBuilderFactoryInterface
   *   A list builder factory object.
   */
  protected function listBuilderFactory() {
    return $this->listBuilderFactory;
  }

  /**
   * Construct an EntityRevisionsListController instance.
   *
   * @param \Drupal\custom_entity_revisions\Entity\EntityRevisionsListBuilderFactoryInterface $list_builder_factory
   *   A list builder factory.
   */
  public function __construct(EntityRevisionsListBuilderFactoryInterface $list_builder_factory) {
    $this->listBuilderFactory = $list_builder_factory;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_revisions.list_builder.factory')
    );
  }

  /**
   * Provides a generic title callback for a list of entity revisions.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Entity\EntityInterface $_entity
   *   (optional) An entity, passed in directly from the request attributes.
   *
   * @return string|null
   *   The title for the entity view page, if an entity was found.
   */
  public function listTitle(RouteMatchInterface $route_match, EntityInterface $_entity = NULL) {
    if ($entity = $this->doGetEntity($route_match, $_entity)) {
      return $this->t('@entity revisions', [
        '@entity' => $entity->label(),
      ]);
    }
  }

  /**
   * Build a list of entity revisions.
   *
   * @return array
   *   A render array.
   */
  public function listing(Request $request) {
    $params = array_filter(array_keys($request->attributes->all()), function ($param) {
      return substr($param, 0, 1) !== '_';
    });

    $entity_type_id = $params[array_keys($params)[count($params) - 1]];
    $entity = $request->get($entity_type_id);

    return $this
      ->listBuilderFactory()
      ->createInstance($entity)
      ->render();
  }

  /**
   * Provides a generic title callback for a single entity revision.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Entity\EntityInterface $_entity
   *   (optional) An entity, passed in directly from the request attributes.
   *
   * @return string|null
   *   The title for the entity view page, if an entity was found.
   */
  public function title(RouteMatchInterface $route_match, EntityInterface $_entity = NULL) {
    if ($entity = $this->doGetEntity($route_match, $_entity)) {
      return $this->t('@entity (revision)', [
        '@entity' => $entity->label(),
      ]);
    }
  }

  /**
   * Render a single entity revision.
   *
   * @param \Drupal\Core\Entity\RevisionableInterface $_entity_revision
   *   The entity revision.
   *
   * @return array
   *   The rendered entity view.
   */
  public function view(RevisionableInterface $_entity_revision) {
    return $this
      ->entityTypeManager()
      ->getViewBuilder($_entity_revision->getEntityTypeId())
      ->view($_entity_revision);
  }

  /**
   * Determines the entity.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   * @param \Drupal\Core\Entity\EntityInterface $_entity
   *   (optional) The entity, set in
   *   \Drupal\Core\Entity\Enhancer\EntityRouteEnhancer.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The entity, if it is passed in directly or if the first parameter of the
   *   active route is an entity; otherwise, NULL.
   */
  protected function doGetEntity(RouteMatchInterface $route_match, EntityInterface $_entity = NULL) {
    if ($_entity) {
      $entity = $_entity;
    }
    else {
      // Let's look up in the route object for the name of upcasted values.
      foreach (array_reverse($route_match->getParameters()->all()) as $parameter) {
        if ($parameter instanceof EntityInterface) {
          $entity = $parameter;
          break;
        }
      }
    }

    if (isset($entity)) {
      return $entity;
    }
    return NULL;
  }

}
