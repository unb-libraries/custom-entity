<?php

namespace Drupal\custom_entity_revisions\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller to respond entity revision routes.
 */
class EntityRevisionsListController extends ControllerBase {

  /**
   * Build a list of entity revisions.
   *
   * @return array
   *   A render array.
   */
  public function listing() {
    return [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Here be a listing of entity revisions.'),
    ];
  }

}
