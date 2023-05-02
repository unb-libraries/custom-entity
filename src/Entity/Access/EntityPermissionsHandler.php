<?php

namespace Drupal\custom_entity\Entity\Access;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityHandlerBase;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Creates permissions for the most common entity operations.
 *
 * @see \Drupal\custom_entity\Entity\Access\EntityPermissionsHandler::getOperations().
 */
class EntityPermissionsHandler extends EntityHandlerBase implements EntityPermissionHandlerInterface, EntityHandlerInterface {

  /**
   * The entity type for which to create permissions.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entityType;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * Get the entity type for which to create permissions.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type definition.
   */
  protected function getEntityType() {
    return $this->entityType;
  }

  /**
   * Get the entity type manager.
   *
   * @return \Drupal\Core\Entity\EntityTypeManagerInterface
   *   An entity type manager.
   */
  protected function entityTypeManager() {
    return $this->entityTypeManager;
  }

  /**
   * Create a new EntityPermissionsHandler instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity type manager.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityType = $entity_type;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    /** @var \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager */
    $entity_type_manager = $container->get('entity_type.manager');
    return new static($entity_type, $entity_type_manager);
  }

  /**
   * {@inheritDoc}
   */
  public function getPermissions() {
    $permissions = [];
    $bundles = ($bundle_type = $this->getEntityType()->getBundleEntityType())
      ? $this->entityTypeManager()
        ->getStorage($bundle_type)
        ->loadMultiple()
      : [];

    foreach ($this->getOperations() as $operation) {
      if (!empty($bundles)) {
        foreach ($bundles as $bundle) {
          $permissions[$this->buildPermissionKey($operation, $bundle)] = $this
            ->buildPermission($operation, $bundle);
        }
      }
      else {
        $permissions[$this->buildPermissionKey($operation)] = $this
          ->buildPermission($operation);
      }
    }
    return $permissions;
  }

  /**
   * Get all operations for which to define a permission.
   *
   * @return string[]
   *   An array of strings.
   */
  protected function getOperations() {
    return ['create', 'list', 'view', 'edit', 'delete'];
  }

  /**
   * Build a permission key for the given operation.
   *
   * @param string $operation
   *   An operation, e.g. 'create', 'edit', etc.
   * @param \Drupal\Core\Config\Entity\ConfigEntityInterface|NULL $bundle
   *   (optional) A bundle of the entity type.
   *
   * @return string
   *   A permission identifier of the form
   *   "{OPERATION} {ENTITY_TYPE_ID} entities".
   */
  protected function buildPermissionKey(string $operation, ConfigEntityInterface $bundle = NULL) {
    return $bundle
      ? "$operation {$bundle->label()} {$this->getEntityType()->id()} entities"
      : "$operation {$this->getEntityType()->id()} entities";
  }

  /**
   * Build a permission definition for the given operation.
   *
   * @param string $operation
   *   An operation, e.g. 'create', 'edit', etc.
   * @param \Drupal\Core\Config\Entity\ConfigEntityInterface|null $bundle
   *   (optional) A bundle of the entity type.
   *
   * @return array
   *   An array containing the following keys:
   *   - title: (string|TranslatableMarkup) The permission title as it shall
   *   appear in the user interface.
   *   - provider: (string) Name of the module under which the permission shall
   *   appear in the user interface. Defaults to the module providing the
   *   entity type.
   */
  protected function buildPermission(string $operation, ConfigEntityInterface $bundle = NULL) {
    return [
      'title' => $bundle
        ? $this->t('@operation @bundle_label @entity_type_plural_label', [
          '@operation' => ucfirst($operation),
          '@bundle_label' => strtolower($bundle->label()),
          '@entity_type_plural_label' => strtolower($this
            ->getEntityType()
            ->getPluralLabel())
        ])
        : $this->t('@operation @entity_type_plural_label', [
          '@operation' => ucfirst($operation),
          '@entity_type_plural_label' => strtolower($this
            ->getEntityType()
            ->getPluralLabel()),
      ]),
      'provider' => $this
        ->getEntityType()
        ->getProvider(),
    ];
  }

}
