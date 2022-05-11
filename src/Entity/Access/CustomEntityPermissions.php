<?php

namespace Drupal\custom_entity\Entity\Access;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Permissions builder for custom entity operations.
 */
class CustomEntityPermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager istance.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Get the module handler.
   *
   * @return \Drupal\Core\Extension\ModuleHandlerInterface
   *   A module handler instance.
   */
  protected function moduleHandler() {
    return $this->moduleHandler;
  }

  /**
   * CustomEntityPermissions constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity type manager instance.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   A module handler instance.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler) {
    $this->entityTypeManager = $entity_type_manager;
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $container->get('entity_type.manager');
    /** @var \Drupal\Core\Extension\ModuleHandlerInterface $module_handler */
    $module_handler = $container->get('module_handler');
    return new static(
      $entity_type_manager,
      $module_handler
    );
  }

  /**
   * Build permissions.
   *
   * @return array
   *   Array permission definitions for each operation defined per each custom
   *   entity using a custom permissions handler.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function permissions() {
    $permissions = [];
    foreach ($this->getEntityTypes() as $entity_type) {
      $permissions_handler = $this
        ->entityTypeManager()
        ->getHandler($entity_type->id(), 'permissions');
      $permissions += $permissions_handler->getPermissions();
    }
    return $permissions;
  }

  /**
   * Get the entity types for which to build permissions.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface[]
   *   An array of entity types defining a custom permissions handler.
   */
  protected function getEntityTypes() {
    $entity_types = $this->entityTypeManager()->getDefinitions();
    return array_filter($entity_types, function (EntityTypeInterface $entity_type) {
      return $this->entityTypeManager()
        ->hasHandler($entity_type->id(), 'permissions');
    });

  }

}
