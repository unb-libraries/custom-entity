<?php

namespace Drupal\custom_entity_revisions\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

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
  public function listing(Request $request) {
    $params = array_filter(array_keys($request->attributes->all()), function ($param) {
      return substr($param, 0, 1) !== '_';
    });

    $entity_type_id = $params[array_keys($params)[0]];
    $entity = $request->get($entity_type_id);

    return [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Here be a listing of @entity revisions.', [
        '@entity' => $entity->label(),
      ]),
    ];
  }

}
