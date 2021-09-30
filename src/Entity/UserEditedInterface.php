<?php

namespace Drupal\custom_entity\Entity;

use Drupal\Core\Session\AccountInterface;

/**
 * Interface for entities that can be edited by users.
 */
interface UserEditedInterface {

  const FIELD_EDITED = 'edited';
  const FIELD_EDITOR = 'editor';

  /**
   * Gets the timestamp of the last entity edit for the current translation.
   *
   * @return int|false
   *   The timestamp of the last entity save operation. FALSE if the field is
   *   not defined.
   */
  public function getEditedTime();

  /**
   * Sets the timestamp of the last entity edit for the current translation.
   *
   * @param int $timestamp
   *   The timestamp of the last entity save operation.
   *
   * @return static
   */
  public function setEditedTime($timestamp);

  /**
   * Get the editor.
   *
   * @return \Drupal\Core\Session\AccountInterface|false
   *   A user entity. FALSE if the field is not defined.
   */
  public function getEditor();

  /**
   * Set the editor.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   A user entity.
   *
   * @return static
   */
  public function setEditor(AccountInterface $user);

}
