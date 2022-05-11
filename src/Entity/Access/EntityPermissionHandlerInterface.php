<?php

namespace Drupal\custom_entity\Entity\Access;

/**
 * Interface for entity permission handlers.
 */
interface EntityPermissionHandlerInterface {

  /**
   * Build an array of permissions based on this handler's entity type.
   *
   * @return array
   *   An array of permission definitions keyed by permission identifiers.
   *
   *   Example:
   *   ========
   *   [
   *     'view node entities' => [
   *       'title' => 'View nodes.',
   *     ],
   *   ]
   */
  public function getPermissions();

}
