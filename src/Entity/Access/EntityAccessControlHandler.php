<?php

namespace Drupal\custom_entity\Entity\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler as DefaultEntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
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
      $account = $this->prepareUser($account);
      $has_access = $this->hasEntityTypePermission($entity->getEntityType(), $operation, $account);
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
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   * @param string $operation
   *   The operation, e.g. "create", "view", "edit", "delete".
   * @param \Drupal\Core\Session\AccountInterface $account
   *   A user account.
   *
   * @return bool
   *   TRUE if the account has the permission to perform the given operation on
   *   the given entity type, i.e. "edit node entities" for a user who
   *   asks for permission to "edit" a "node" entity.
   *   FALSE otherwise.
   */
  protected function hasEntityTypePermission(EntityTypeInterface $entity_type, string $operation, AccountInterface $account) {
    $required_permission = "$operation {$entity_type->id()} entities";
    return $account->hasPermission($required_permission);
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
