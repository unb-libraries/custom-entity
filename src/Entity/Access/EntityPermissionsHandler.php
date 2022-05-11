<?php

namespace Drupal\custom_entity\Entity\Access;

use Drupal\Core\Entity\EntityHandlerBase;
use Drupal\Core\Entity\EntityHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
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
   * Get the entity type for which to create permissions.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type definition.
   */
  protected function getEntityType() {
    return $this->entityType;
  }

  /**
   * Create a new EntityPermissionsHandler instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type definition.
   */
  public function __construct(EntityTypeInterface $entity_type) {
    $this->entityType = $entity_type;
  }

  /**
   * {@inheritDoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static($entity_type);
  }

  /**
   * {@inheritDoc}
   */
  public function getPermissions() {
    $permissions = [];
    foreach ($this->getOperations() as $operation) {
      $permissions[$this->buildPermissionKey($operation)] = $this
        ->buildPermission($operation);
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
   *
   * @return string
   *   A permission identifier of the form
   *   "{OPERATION} {ENTITY_TYPE_ID} entities".
   */
  protected function buildPermissionKey(string $operation) {
    return "$operation {$this->getEntityType()->id()} entities";
  }

  /**
   * Build a permission definition for the given operation.
   *
   * @param string $operation
   *   An operation, e.g. 'create', 'edit', etc.
   *
   * @return array
   *   An array containing the following keys:
   *   - title: (string|TranslatableMarkup) The permission title as it shall
   *   appear in the user interface.
   *   - provider: (string) Name of the module under which the permission shall
   *   appear in the user interface. Defaults to the module providing the
   *   entity type.
   */
  protected function buildPermission(string $operation) {
    return [
      'title' => $this->t('@operation @entity_type_plural_label.', [
        '@operation' => $operation,
        '@entity_type_plural_label' => $this
          ->getEntityType()
          ->getPluralLabel(),
      ]),
      'provider' => $this
        ->getEntityType()
        ->getProvider(),
    ];
  }

}
