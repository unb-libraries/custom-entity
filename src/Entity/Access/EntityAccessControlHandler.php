<?php

namespace Drupal\custom_entity\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler as DefaultEntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides access checking for custom entities.
 *
 * Supports any operation for which a permission of
 * the form "OPERATION ENTITY_TYPE entities" is defined.
 *
 * Example:
 * A route which requires "_entity_access": "node.view"
 * will be granted access to any user who has the
 * "view node entities" permission.
 */
class EntityAccessControlHandler extends DefaultEntityAccessControlHandler {

  /**
   * {@inheritDoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    $access = parent::checkAccess($entity, $operation, $account);

    // If explicit access has already been granted, it means the user has admin
    // permissions. If access has been explicitly denied, further checks are
    // equally redundant.
    if ($access->isNeutral()) {
      $entity_type = $entity->getEntityType();
      $account = $this->prepareUser($account);

      $bundle = NULL;
      if ($entity_type->getBundleEntityType()) {
        $bundle_key = $entity_type->getKey('bundle');
        $bundle = $entity->get($bundle_key)->entity;
      }

      $has_access = $this->hasEntityTypePermission($operation, $account, $entity_type, $bundle);
      if ($has_access && $entity_access_callback = $this->getEntityPermissionCallback($operation)) {
        $has_access &= call_user_func($entity_access_callback, $entity, $account);
      }

      // @todo Cache access checking.
      $access = $has_access
        ? AccessResult::allowed()
        : AccessResult::forbidden();
    }

    return $access;
  }

  /**
   * Whether access should be granted based on the given entity type.
   *
   * @param string $operation
   *   The operation, e.g. "create", "view", "edit", "delete".
   * @param \Drupal\Core\Session\AccountInterface $account
   *   A user account.
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   * @param \Drupal\Core\Config\Entity\ConfigEntityInterface|null $bundle
   *   (optional) The entity bundle.
   *
   * @return bool
   *   If a bundle is provided, TRUE is returned if the account has the permission to perform the given operation on
   *   the given entity type and bundle, or for all bundles of the entity type. For example, a user who asks for
   *   permission to "edit" a "node" entity with content type "page" requires either permission to
   *   "edit node:page entities" or "edit node entities". If no bundle is provided, TRUE is returned if the latter
   *   permission exists for the given account.
   */
  protected function hasEntityTypePermission(string $operation, AccountInterface $account, EntityTypeInterface $entity_type, ConfigEntityInterface $bundle = NULL) {
    $entity_permission = "$operation {$entity_type->id()} entities";
    if ($bundle) {
      $bundle_permission = "$operation {$entity_type->id()}:{$bundle->id()} entities";
      return $account->hasPermission($bundle_permission) || $account->hasPermission($entity_permission);
    }
    return $account->hasPermission($entity_permission);
  }

  /**
   * Get a callback to check entity based access for the given operation.
   *
   * @param string $operation
   *   A string.
   *
   * @return Callable|false
   *   A callback. FALSE if no callable entity based permission check is
   *   defined for the given entity.
   */
  protected function getEntityPermissionCallback(string $operation) {
    $callback_name = 'hasEntity' . ucfirst(strtolower($operation)) . 'Access';
    if (is_callable($callback = [$this, $callback_name])) {
      return $callback;
    }
    return FALSE;
  }

  /**
   * Performs create access checks.
   *
   * Overrides DefaultEntityAccessControlHandler::checkCreateAccess. This
   * performs the same access check on 'create' as on any other
   * operation.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user for which to check access.
   * @param array $context
   *   An array of key-value pairs to pass additional context when needed.
   * @param string|null $entity_bundle
   *   (optional) The bundle of the entity. Required if the entity supports
   *   bundles, defaults to NULL otherwise.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $access = parent::checkCreateAccess($account, $context, $entity_bundle);
    if (!$access->isForbidden() && $entity_type_id = $context['entity_type_id']) {
      $required_permission = "create $entity_type_id entities";
      $access = AccessResult::allowedIfHasPermission($account, $required_permission);
    }
    return $access;
  }

}
