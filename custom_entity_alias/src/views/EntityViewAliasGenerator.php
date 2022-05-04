<?php

namespace Drupal\custom_entity_alias\views;

use Drupal\Core\Entity\EntityInterface;
use Drupal\pathauto\AliasCleanerInterface;
use Drupal\pathauto\AliasStorageHelperInterface;
use Drupal\pathauto\AliasUniquifierInterface;
use Drupal\Core\Utility\Token;
use Drupal\token\TokenEntityMapperInterface;
use Drupal\views\ViewEntityInterface;

/**
 * Generates path aliases for custom entity views.
 */
class EntityViewAliasGenerator implements EntityViewAliasGeneratorInterface {

  /**
   * The token replacer.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * The token entity mapper.
   *
   * @var \Drupal\token\TokenEntityMapperInterface
   */
  protected $tokenEntityMapper;

  /**
   * The alias cleaner.
   *
   * @var \Drupal\pathauto\AliasCleanerInterface
   */
  protected $aliasCleaner;

  /**
   * The alias uniquifier.
   *
   * @var \Drupal\pathauto\AliasUniquifierInterface
   */
  protected $aliasUniquifier;

  /**
   * The alias storage helper.
   *
   * @var \Drupal\pathauto\AliasStorageHelperInterface
   */
  protected $aliasStorageHelper;

  /**
   * Get the token replacer.
   *
   * @return \Drupal\Core\Utility\Token
   *   A token replacer.
   */
  protected function token() {
    return $this->token;
  }

  /**
   * Get the token entity mapper.
   *
   * @return \Drupal\token\TokenEntityMapperInterface
   *   A token entity mapper.
   */
  protected function tokenEntityMapper() {
    return $this->tokenEntityMapper;
  }

  /**
   * Get the alias cleaner.
   *
   * @return \Drupal\pathauto\AliasCleanerInterface
   *   An alias cleaner.
   */
  protected function aliasCleaner() {
    return $this->aliasCleaner;
  }

  /**
   * Get the alias uniquifier.
   *
   * @return \Drupal\pathauto\AliasUniquifierInterface
   *   An alias uniquifier.
   */
  protected function aliasUniquifier() {
    return $this->aliasUniquifier;
  }

  /**
   * Get the alias storage helper.
   *
   * @return \Drupal\pathauto\AliasStorageHelperInterface
   *   An alias storage helper.
   */
  protected function aliasStorageHelper() {
    return $this->aliasStorageHelper;
  }

  /**
   * Create a new EntityViewAliasGenerator instance.
   *
   * @param \Drupal\Core\Utility\Token $token
   *   A token replacer.
   * @param \Drupal\token\TokenEntityMapperInterface $token_entity_mapper
   *   A token entity mapper.
   * @param \Drupal\pathauto\AliasCleanerInterface $alias_cleaner
   *   An alias cleaner.
   * @param \Drupal\pathauto\AliasUniquifierInterface $alias_uniquifier
   *   An alias uniquifier.
   * @param \Drupal\pathauto\AliasStorageHelperInterface $alias_storage_helper
   *   An alias storage helper.
   */
  public function __construct(Token $token, TokenEntityMapperInterface $token_entity_mapper, AliasCleanerInterface $alias_cleaner, AliasUniquifierInterface $alias_uniquifier, AliasStorageHelperInterface $alias_storage_helper) {
    $this->token = $token;
    $this->tokenEntityMapper = $token_entity_mapper;
    $this->aliasCleaner = $alias_cleaner;
    $this->aliasUniquifier = $alias_uniquifier;
    $this->aliasStorageHelper = $alias_storage_helper;
  }

  /**
   * {@inheritDoc}
   */
  public function generateViewAlias(ViewEntityInterface $view, EntityInterface $entity, array $options = []) {
    $source = $this->resolveContextualPath($view, $entity, array_merge($options, ['alias' => 'id']));
    $alias = $this->resolveContextualPath($view, $entity, $options);

    $this->buildAlias($source, $alias, $view->language()->getId());
  }

  /**
   * Build a path by resolving the view entity relationship context.
   *
   * @param \Drupal\views\ViewEntityInterface $view
   *   The view.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param array $options
   *   (optional) An array of options:
   *   - alias: (string) The field ID of the given entity to use to generate an
   *   alias. Defaults to the entity's label field.
   *   - display: (string) A display ID of the view. Defaults to "page_1".
   *
   * @return string
   *   A string representing a path to a view display without where any contexts
   *   have been replaced by actual values.
   */
  protected function resolveContextualPath(ViewEntityInterface $view, EntityInterface $entity, array $options = []) {
    $options = array_merge([
      'alias' => $entity->getEntityType()->getKey('label'),
      'context' => "%{$entity->getEntityTypeId()}",
      'display' => 'page_1',
    ], $options);

    $entity_type_id = $entity->getEntityTypeId();
    $path = $view->getDisplay($options['display'])['display_options']['path'];

    $text = str_replace("{$options['context']}", "[$entity_type_id:{$options['alias']}]", $path);
    $data = [
      $this->tokenEntityMapper()->getEntityTypeForTokenType($entity_type_id) => $entity,
    ];
    $options = [
      'clear' => TRUE,
      'callback' => [$this->aliasCleaner(), 'cleanTokenValues'],
      'langcode' => $view->language()->getId(),
      'pathauto' => TRUE,
    ];

    return $this
      ->token()
      ->replace($text, $data, $options);
  }

  /**
   * Create an alias for the given source.
   *
   * @param string $source
   *   An existing system path.
   * @param string $alias
   *   A new path.
   * @param string $language
   *   A language code.
   */
  protected function buildAlias(string $source, string $alias, string $language) {
    $clean_source = $this->aliasCleaner()->cleanAlias($source);
    $clean_alias = $this->aliasCleaner()->cleanAlias($alias);

    $this->aliasUniquifier()->uniquify($clean_alias, $clean_source, $language);
    $this->aliasStorageHelper()->save([
      'source' => $clean_source,
      'alias' => $clean_alias,
      'language' => $language,
    ], NULL, 'insert');
  }

}
