<?php

namespace Drupal\custom_entity_ui\Form;

use Drupal\Core\Form\FormInterface;

/**
 * Interface for entity type settings forms.
 *
 * @package Drupal\custom_entity_ui\Form
 */
interface EntityTypeSettingsFormInterface extends FormInterface {

  /**
   * Form title callback.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|string
   *   A (translatable) string.
   */
  public function getTitle();

}
