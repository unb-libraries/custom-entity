<?php

namespace Drupal\custom_entity_ui\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\custom_entity_ui\Form\EntityTypeSettingsForm;
use Drupal\custom_entity_ui\Form\EntityTypeSettingsFormInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Route subscriber that adds "settings" route for each entity type.
 *
 * @package Drupal\custom_entity_ui\Entity\Routing
 */
class EntitySettingsRouteSubscriber extends RouteSubscriberBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Get the entity type manager service.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Constructs a RouteSubscriber object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    foreach ($this->entityTypeManager()->getDefinitions() as $entity_type_id => $entity_type) {
      if (!$entity_type->getBundleEntityType() && $settings_route = $this->getSettingsRoute($entity_type)) {
        $collection->add("entity.{$entity_type_id}.settings", $settings_route);
      }
    }
  }

  /**
   * Build a "settings" route for the given entity type.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return \Symfony\Component\Routing\Route|void
   *   A route object.
   */
  protected function getSettingsRoute(EntityTypeInterface $entity_type) {
    if (!$entity_type->getBundleEntityType()) {
      $form_class = $this->getFormClass($entity_type);
      $defaults = [
        'entity_type_id' => $entity_type->id(),
        '_form' => $form_class,
        '_title_callback' => $form_class . '::getTitle',
      ];

      $requirements = [
        '_permission' => $this->getPermission($entity_type),
      ];

      return new Route($this->getPath($entity_type), $defaults, $requirements);
    }
  }

  /**
   * Get the route path.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return string
   *   A string.
   */
  protected function getPath(EntityTypeInterface $entity_type) {
    if ($path = $entity_type->getLinkTemplate('settings')) {
      return $path;
    }
    return "/admin/structure/custom-types/{$entity_type->id()}/settings";
  }

  /**
   * Get the form class name.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return string
   *   A fully namespaced class name.
   */
  protected function getFormClass(EntityTypeInterface $entity_type) {
    $form_class = $entity_type->getFormClass('settings');
    if ($form_class && is_subclass_of($form_class, EntityTypeSettingsFormInterface::class)) {
      return $form_class;
    }
    return EntityTypeSettingsForm::class;
  }

  /**
   * Get the permission to access the settings route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   *
   * @return string
   *   A string.
   */
  protected function getPermission(EntityTypeInterface $entity_type) {
    return "administer {$entity_type->id()} entities";
  }

}
