<?php

namespace Drupal\custom_entity_ui\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for entity type settings forms.
 *
 * @package Drupal\custom_entity_ui\Form
 */
class EntityTypeSettingsForm extends ConfigFormBase implements EntityTypeSettingsFormInterface {

  /**
   * The entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface
   */
  protected $entityType;

  /**
   * Get the entity type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   An entity type.
   */
  protected function getEntityType() {
    return $this->entityType;
  }

  /**
   * EntityTypeSettingsForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   An entity type.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(EntityTypeInterface $entity_type, ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
    $this->entityType = $entity_type;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    $route_matcher = $container->get('current_route_match');
    $entity_type_id = $route_matcher
      ->getRouteObject()
      ->getDefault('entity_type_id');
    $entity_type = $container->get('entity_type.manager')
      ->getDefinition($entity_type_id);

    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $container->get('config.factory');

    return new static($entity_type, $config_factory);
  }

  /**
   * Get the config object containing the settings for the entity type.
   *
   * @return \Drupal\Core\Config\Config
   *   An editable config object.
   */
  protected function getEditableEntityConfig() {
    return $this->config(
      $this->getEditableConfigNames()['entity_settings']);
  }

  /**
   * {@inheritDoc}
   */
  protected function getEditableConfigNames() {
    $module = $this->getEntityType()->getProvider();
    $entity_type_id = $this->getEntityType()->id();
    return [
      'entity_settings' => "{$module}.{$entity_type_id}.settings",
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    $entity_type_id = $this->getEntityType()->id();
    return "{$entity_type_id}.settings_form";
  }

  /**
   * {@inheritDoc}
   */
  public function getTitle() {
    return $this->t('@entity_type Settings', [
      '@entity_type' => $this->getEntityType()->getLabel(),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    $this->save($form, $form_state);
    parent::submitForm($form, $form_state);
  }

  /**
   * Save to settings.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  protected function save(array &$form, FormStateInterface $form_state) {
    $settings = $this->getEditableEntityConfig();
    foreach ($form_state->getValues() as $key => $value) {
      $settings->set($key, $value);
    }
    $settings->save();
  }

}
