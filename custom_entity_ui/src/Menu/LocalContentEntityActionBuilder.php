<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Base class for local content entity action builders.
 *
 * @package Drupal\custom_entity_ui\Menu
 */
abstract class LocalContentEntityActionBuilder implements LocalEntityActionBuilderInterface {

  use StringTranslationTrait;

  /**
   * The route provider.
   *
   * @var \Drupal\Core\Routing\RouteProviderInterface
   */
  protected $routeProvider;

  /**
   * Get the route provider.
   *
   * @return \Drupal\Core\Routing\RouteProviderInterface
   *   A route provider object.
   */
  protected function routeProvider() {
    return $this->routeProvider;
  }

  /**
   * Create a LocalContentEntityActionBuilder instance.
   *
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   A route provider object.
   */
  public function __construct(RouteProviderInterface $route_provider) {
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritDoc}
   */
  public function buildLocalAction(EntityTypeInterface $entity_type, string $bundle = '') {
    return [
      'id' => $this->getId($entity_type, $bundle),
      'title' => $this->getTitle($entity_type, $bundle),
      'route_name' => $this->getTargetRouteId($entity_type, $bundle),
      'route_parameters' => $this->getTargetRouteParameters($entity_type, $bundle),
      'class' => DestinationAwareLocalAction::class,
      'options' => [],
      'appears_on' => $this->getAppearsOn($entity_type, $bundle),
      'weight' => 0,
    ];
  }

  /**
   * Whether the route with the given name exists.
   *
   * @param string $route_name
   *   A route name.
   *
   * @return bool
   *   TRUE if the route provider provides a route with the given name.
   *   FALSE otherwise.
   */
  protected function doesRouteExist($route_name) {
    try {
      return !is_null($this->routeProvider()
        ->getRouteByName($route_name));
    }
    catch (RouteNotFoundException $e) {
      return FALSE;
    }
  }

  /**
   * Get the ID of the local action.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   * @param string $bundle
   *   (optional) A bundle, if the entity type supports bundles.
   *
   * @return string
   *   A string.
   */
  abstract protected function getId(EntityTypeInterface $entity_type, string $bundle = '');

  /**
   * Get the label of the local action.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   * @param string $bundle
   *   (optional) A bundle, if the entity type supports bundles.
   *
   * @return string|\Drupal\Core\StringTranslation\TranslatableMarkup
   *   A (translatable) string.
   */
  abstract protected function getTitle(EntityTypeInterface $entity_type, string $bundle = '');

  /**
   * Get the ID of the route the action should point at.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   * @param string $bundle
   *   (optional) A bundle, if the entity type supports bundles.
   *
   * @return string
   *   A route ID.
   */
  abstract protected function getTargetRouteId(EntityTypeInterface $entity_type, string $bundle = '');

  /**
   * Get any parameters to pass to the target route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   * @param string $bundle
   *   (optional) A bundle, if the entity type supports bundles.
   *
   * @return array
   *   An array of route parameter definitions.
   */
  protected function getTargetRouteParameters(EntityTypeInterface $entity_type, string $bundle = '') {
    $entity_type_id = $entity_type->id();
    if (!$entity_type->hasKey('bundle')) {
      return [];
    }

    return [
      $entity_type->getBundleEntityType() => "{$entity_type_id}:{$bundle}",
    ];
  }

  /**
   * Get the route IDs the action should appear on.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type.
   * @param string $bundle
   *   (optional) A bundle, if the entity type supports bundles.
   *
   * @return array
   *   An array of route IDs.
   */
  abstract protected function getAppearsOn(EntityTypeInterface $entity_type, string $bundle = '');

  /**
   * {@inheritDoc}
   */
  public function applies(EntityTypeInterface $entity_type, string $bundle = '') {
    return $entity_type->getGroup() === 'content'
      && $this->doesRouteExist($this->getTargetRouteId($entity_type, $bundle));
  }

}
