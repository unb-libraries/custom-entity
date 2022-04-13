<?php

namespace Drupal\custom_entity_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller rendering a list of custom entity types.
 *
 * @package Drupal\custom_entity_ui\Controller
 */
abstract class EntityTypeListController extends ControllerBase {

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
   * Create a new EntityTypeListController instance.
   *
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   A route provider.
   */
  public function __construct(RouteProviderInterface $route_provider) {
    $this->routeProvider = $route_provider;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Routing\RouteProviderInterface $route_provider */
    $route_provider = $container->get('router.route_provider');
    return new static($route_provider);
  }

  /**
   * Creates a list of entity types.
   *
   * @return array
   *   A render element.
   */
  abstract public function listing();

  /**
   * Load the entity types to contain in this listing.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface[]
   *   An array of entity types keyed by their machine name.
   */
  protected function loadEntityTypes() {
    $entity_types = $this->entityTypeManager()->getDefinitions();
    return array_filter($entity_types, [$this, 'filter']);
  }

  /**
   * Whether the given entity type should be contained in the listing.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return bool
   *   TRUE if the entity type passes the filter, i.e. is contained.
   *   FALSE if it is excluded.
   */
  protected function filter(EntityTypeInterface $entity_type) {
    if ($entity_type->getGroup() !== 'content') {
      return FALSE;
    }

    if ($field_ui_base_route = $entity_type->get('field_ui_base_route')) {
      if ($this->routeProvider()->getRouteByName($field_ui_base_route)) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Whether the entity type has bundles.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return bool
   *   TRUE if the entity type can be bundled, even if no instances of the
   *   bundle type exist. FALSE if the entity type cannot be bundled.
   */
  protected function isBundled(EntityTypeInterface $entity_type) {
    return !is_null($entity_type->getBundleEntityType());
  }

  /**
   * Get bundle instances for the entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Config\Entity\ConfigEntityInterface[]
   *   An array of bundle entities.
   */
  protected function getBundles(EntityTypeInterface $entity_type) {
    if ($storage = $this->getBundleStorage($entity_type)) {
      /** @var \Drupal\Core\Config\Entity\ConfigEntityInterface[] $bundles */
      $bundles = $storage->loadMultiple();
      return $bundles;
    }
    else {
      $this->messenger()
        ->addError($this->t("@entity_type uses a bundle type that does not exist.", [
          '@entity_type' => $entity_type->getLabel(),
        ]));
      return [];
    }
  }

  /**
   * Get the storage handler for the entity type's bundle type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Drupal\Core\Entity\EntityStorageInterface|null
   *   An entity storage handler. NULL if none could be found, e.g. the
   *   defined bundle type does not exist or references a storage handler
   *   that does not exist.
   */
  protected function getBundleStorage(EntityTypeInterface $entity_type) {
    try {
      $bundle_type_id = $entity_type->getBundleEntityType();
      return $this->entityTypeManager()
        ->getStorage($bundle_type_id);
    }
    catch (\Exception $e) {
      return NULL;
    }
  }

}
