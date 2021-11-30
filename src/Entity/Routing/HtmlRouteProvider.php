<?php

namespace Drupal\custom_entity\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\Routing\Route;

/**
 * Enhances Drupal's default HTML route provider.
 *
 * Adds the following routes:
 * - delete-all-form
 * - custom routes for each "*-form" handler and link template.
 *
 * Adds the module providing the route as a dependency to each route.
 */
class HtmlRouteProvider extends DefaultHtmlRouteProvider {

  use StringTranslationTrait;

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);
    $entity_type_id = $entity_type->id();

    foreach ($this->getCustomFormRoutes($entity_type) as $route_name => $route) {
      $routes->add($route_name, $route);
    }

    if ($delete_all_route = $this->getDeleteAllFormRoute($entity_type)) {
      $routes->add("entity.{$entity_type_id}.delete_all", $delete_all_route);
    }

    foreach ($routes->all() as $route) {
      $route->setRequirement('_module_dependencies', $entity_type->getProvider());
    }

    return $routes;
  }

  /**
   * Gets the collection route.
   *
   * Overrides DefaultHtmlRouteProvider::getCollectionRoute.
   * This removes the admin_permission requirement from the route
   * and replaces it by checking for "list ENTITY_TYPE entities" permission.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('collection') && $entity_type->hasListBuilderClass()) {
      $entity_type_id = $entity_type->id();
      /** @var \Drupal\Core\StringTranslation\TranslatableMarkup $label */
      $label = $entity_type->getCollectionLabel();

      $route = new Route($entity_type->getLinkTemplate('collection'));
      $route
        ->addDefaults([
          '_entity_list' => $entity_type_id,
          '_title' => $label->getUntranslatedString(),
          '_title_arguments' => $label->getArguments(),
          '_title_context' => $label->getOption('context'),
        ])
        ->setRequirement('_permission', "list {$entity_type_id} entities");

      return $route;
    }
    return NULL;
  }

  /**
   * Create routes for no-default templates.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return array
   *   An array of routes, keyed by each route's name.
   *
   * @see \Drupal\custom_entity\Entity\Routing\HtmlRouteProvider::isCustomFormLink()
   */
  protected function getCustomFormRoutes(EntityTypeInterface $entity_type) {
    $routes = [];
    $entity_type_id = $entity_type->id();
    foreach ($entity_type->getLinkTemplates() as $route_type => $link_template) {
      if ($this->isCustomFormLink($entity_type, $route_type) && $this->isSingleEntityPath($entity_type, $link_template)) {
        $operation = substr($route_type, 0, -5);
        $route = new Route($link_template);
        $route->addDefaults([
          '_entity_form' => "{$entity_type_id}.{$operation}",
          '_title' => sprintf('%s %s', ucfirst($operation), $entity_type->getSingularLabel()),
        ]);
        $route->setRequirement('_entity_access', "{$entity_type_id}.{$operation}");
        $route->setOption('parameters', [
          $entity_type_id => ['type' => 'entity:' . $entity_type_id],
        ]);
        $route_name = str_replace('-', '_',
          "entity.{$entity_type_id}.{$operation}_form");
        $routes[$route_name] = $route;
      }
    }
    return $routes;
  }

  /**
   * Whether the given link template leads to a non-default form.
   *
   * Default link templates are:
   * - add-form
   * - edit-form
   * - delete-form
   * - delete-all-form.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type that defines the link template.
   * @param string $link_template
   *   The link template.
   *
   * @return bool
   *   TRUE if the given link template leads to a form route.
   *   FALSE otherwise.
   */
  protected function isCustomFormLink(EntityTypeInterface $entity_type, $link_template) {
    $default_templates = [
      'add-form',
      'edit-form',
      'delete-form',
      'delete-all-form',
    ];

    if (preg_match('/(\w)(-\w)*-form/', $link_template) && !in_array($link_template, $default_templates)) {
      $operation = substr($link_template, 0, -5);
      return !is_null($entity_type->getFormClass($operation));
    }
    return FALSE;
  }

  /**
   * Whether the given route path involves a single entity instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type that defines the route path.
   * @param string $path
   *   The route path.
   *
   * @return bool
   *   TRUE if the given route path contains a route parameter
   *   that matches the given entity type. FALSE otherwise.
   */
  protected function isSingleEntityPath(EntityTypeInterface $entity_type, $path) {
    $entity_type_id = $entity_type->id();
    return boolval(preg_match("/.*\/\{$entity_type_id\}\/.*/", $path));
  }

  /**
   * Gets the "delete-all" route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getDeleteAllFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('delete-all-form')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('delete-all-form'));
      $route->addDefaults([
        '_form' => $entity_type->getFormClass('delete-all'),
        '_title' => sprintf('Delete all %s', $entity_type->getPluralLabel()),
        'entity_type_id' => $entity_type_id,
      ])
        ->setRequirement('_permission', "delete all {$entity_type_id} entities");

      return $route;
    }
    return NULL;
  }

}
