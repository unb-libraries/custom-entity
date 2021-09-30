<?php

namespace Drupal\custom_entity\Plugin\Field\FieldType;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\Core\TypedData\TypedDataInterface;

/**
 * Defines the 'creator' entity field type.
 *
 * This builds upon the "entity_reference" field type and always references
 * the "user" entity. Similar to the "created" field type, any field value will
 * persist once initialized. An initial value is assigned upon creation of the
 * entity.
 *
 * @FieldType(
 *   id = "creator",
 *   label = @Translation("Creator"),
 *   description = @Translation("An entity field unchangeably referencing a user entity."),
 *   category = @Translation("Reference"),
 *   no_ui = TRUE,
 *   cardinality = 1,
 *   list_class = "\Drupal\Core\Field\EntityReferenceFieldItemList",
 *   default_formatter = "entity_reference_label"
 * )
 */
class Creator extends EntityReferenceItem {

  /**
   * The currently logged-in user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Get the currently logged-in user.
   *
   * @return \Drupal\Core\Session\AccountProxyInterface
   *   A user account.
   */
  protected function currentUser() {
    return $this->currentUser;
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(DataDefinitionInterface $definition, $name = NULL, TypedDataInterface $parent = NULL) {
    parent::__construct($definition, $name, $parent);
    // @todo Use proper dependency injection once field types support it.
    $this->currentUser = \Drupal::currentUser();
  }

  /**
   * {@return array}
   */
  public static function defaultStorageSettings() {
    return [
      'target_type' => 'user',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $user_field_definition = BaseFieldDefinition::createFromFieldStorageDefinition($field_definition)
      ->setSetting('target_type', 'user');
    return parent::propertyDefinitions($user_field_definition);
  }

  /**
   * @param string $setting_name
   *
   * @return mixed|string
   */
  protected function getSetting($setting_name) {
    if ($setting_name === 'target_type') {
      return 'user';
    }
    return parent::getSetting($setting_name);
  }

  /**
   * {@inheritDoc}
   */
  public function applyDefaultValue($notify = TRUE) {
    parent::applyDefaultValue($notify);
    $this->setValue(['target_id' => $this->currentUser()->id()], $notify);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    return [];
  }

  /**
   * {@inheritDoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    return [];
  }

}
