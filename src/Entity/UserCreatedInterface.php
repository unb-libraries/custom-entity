<?php

namespace Drupal\custom_entity\Entity;

use Drupal\Core\Entity\FieldableEntityInterface;

/**
 * Interface for entities that can be created by users.
 */
interface UserCreatedInterface extends FieldableEntityInterface {

  const FIELD_CREATED = 'created';
  const FIELD_CREATOR = 'creator';

  /**
   * Gets the timestamp of the entity creation for the current translation.
   *
   * @return int|false
   *   The timestamp of the last entity save operation. FALSE if the field
   *   is not defined.
   */
  public function getCreatedTime();

  /**
   * Get the creator.
   *
   * @return \Drupal\Core\Session\AccountInterface|false
   *   A user entity. FALSE if the field is not defined.
   */
  public function getCreator();

}
