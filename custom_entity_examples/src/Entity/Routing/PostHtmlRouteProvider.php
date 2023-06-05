<?php

namespace Drupal\custom_entity_examples\Entity\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\custom_entity\Entity\Routing\HtmlRouteProvider;
use Drupal\custom_entity_revisions\Entity\Routing\RevisionsRouteProviderTrait;

/**
 * HTML route provider for "Post" entities.
 */
class PostHtmlRouteProvider extends HtmlRouteProvider {

  use RevisionsRouteProviderTrait;

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);

    $routes->addCollection($this->getAllRevisionRoutes($entity_type));
    foreach ($routes as $route) {
      if (!$parameters = $route->getOption('parameters')) {
        $parameters = [];
      }
      $parameters['cex_blog'] = [
        'type' => 'entity:cex_blog',
      ];
      $route->setOption('parameters', $parameters);
    }

    return $routes;
  }

}
