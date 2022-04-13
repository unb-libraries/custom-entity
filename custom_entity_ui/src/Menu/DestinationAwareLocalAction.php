<?php

namespace Drupal\custom_entity_ui\Menu;

use Drupal\Core\Menu\LocalActionDefault;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Routing\RouteProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Local action handler allowing a return to origin.
 *
 * @package Drupal\custom_entity_ui\Menu
 */
class DestinationAwareLocalAction extends LocalActionDefault {

  /**
   * The current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $currentRequest;

  /**
   * Get the current request.
   *
   * @return \Symfony\Component\HttpFoundation\Request
   *   A request object.
   */
  protected function currentRequest() {
    return $this->currentRequest;
  }

  /**
   * Create a LocalActionDefault instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteProviderInterface $route_provider
   *   The route provider to load routes by name.
   * @param Request $current_request
   *   The current request.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteProviderInterface $route_provider, Request $current_request) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $route_provider);
    $this->currentRequest = $current_request;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var \Drupal\Core\Routing\RouteProviderInterface $route_provider */
    $route_provider = $container->get('router.route_provider');
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $route_provider,
      $container->get('request_stack')
        ->getCurrentRequest()
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getOptions(RouteMatchInterface $route_match) {
    $options = parent::getOptions($route_match);
    if (!array_key_exists('query', $options)) {
      $options['query'] = [];
    }
    $options['query']['destination'] = $this
      ->getCurrentRequestPath();
    return $options;
  }

  /**
   * Get the currently requested path.
   *
   * @return string
   *   A string of the form "/current/path".
   */
  protected function getCurrentRequestPath() {
    return $this->currentRequest()->getPathInfo();
  }

  /**
   * {@inheritDoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    $contexts[] = 'url.path';
    return $contexts;
  }

}
