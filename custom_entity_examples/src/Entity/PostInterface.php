<?php

namespace Drupal\custom_entity_examples\Entity;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Interface for "Post" entities.
 */
interface PostInterface extends ContentEntityInterface {

  /**
   * Gets the blog this post belongs to.
   *
   * @return \Drupal\custom_entity_examples\Entity\Blog
   *   The blog this post belongs to.
   */
  public function getBlog();

  /**
   * Sets the blog this post belongs to.
   *
   * @param \Drupal\custom_entity_examples\Entity\Blog $blog
   *   The blog this post belongs to.
   *
   * @return $this
   */
  public function setBlog(Blog $blog);

}
