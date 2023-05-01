<?php

namespace Drupal\custom_entity_examples\Form;

use Drupal\Core\Entity\ContentEntityForm;

/**
 * Default form for "Post" entities.
 */
class PostForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  protected function prepareEntity() {
    parent::prepareEntity();
    if (!$this->entity->getBlog()) {
      $blog = $this
        ->getRouteMatch()
        ->getParameter('cex_blog');
      $this->entity->set('blog', $blog);
    }
  }

}
