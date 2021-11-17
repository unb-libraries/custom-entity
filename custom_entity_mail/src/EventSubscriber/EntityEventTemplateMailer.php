<?php

namespace Drupal\custom_entity_mail\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event subscriber sending template-based emails upon entity events.
 *
 * @package Drupal\custom_entity_mail\EventSubscriber
 */
abstract class EntityEventTemplateMailer extends EntityEventMailer {

  /**
   * The host and scheme part of the entity URL.
   *
   * @var string
   */
  protected $entityUrlBase;

  /**
   * Get the host and scheme part of the entity URL.
   *
   * @return string
   *   A string, e.g. https://example.com.
   */
  protected function getEntityUrlBase() {
    return $this->entityUrlBase;
  }

  /**
   * EntityEventTemplateMailer constructor.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   A mail manager instance.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param string $entity_type_id
   *   A string.
   */
  public function __construct(MailManagerInterface $mail_manager, Request $request, string $entity_type_id) {
    parent::__construct($mail_manager, $entity_type_id);
    $this->entityUrlBase = $request->getSchemeAndHttpHost();
  }

  /**
   * {@inheritDoc}
   */
  protected function getSubject(EntityInterface $entity, string $key) {
    if ($template_path = $this->getSubjectTemplate($entity, $key)) {
      return [
        'template' => $template_path,
        'context' => $this->getSubjectContext($entity, $key),
      ];
    }
    return "";
  }

  /**
   * Find a subject template for the given entity and key.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The message key.
   *
   * @return string|false
   *   A path to an existing template file, excluding potential file extensions
   *   from a template engine. FALSE if no suitable template can be found.
   */
  protected function getSubjectTemplate(EntityInterface $entity, string $key) {
    $provider = $entity->getEntityType()->getProvider();
    $path = drupal_get_path('module', $provider) . "/templates/{$key}.subject";
    if (!empty(glob("$path*"))) {
      return $path;
    }
    return FALSE;
  }

  /**
   * The context to pass to a renderer when rendering the subject template.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The message key.
   *
   * @return array
   *   An array of variable names and values.
   */
  protected function getSubjectContext(EntityInterface $entity, string $key) {
    return [
      $entity->getEntityTypeId() => $entity,
    ];
  }

  /**
   * {@inheritDoc}
   */
  protected function getBody(EntityInterface $entity, string $key) {
    if ($template_path = $this->getBodyTemplate($entity, $key)) {
      return [
        'template' => $template_path,
        'context' => $this->getBodyContext($entity, $key),
      ];
    }
    return "";
  }

  /**
   * Find a body template for the given entity and key.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The message key.
   *
   * @return string|false
   *   A path to an existing template file, excluding potential file extensions
   *   from a template engine. FALSE if no suitable template can be found.
   */
  protected function getBodyTemplate(EntityInterface $entity, string $key) {
    $provider = $entity->getEntityType()->getProvider();
    $path = drupal_get_path('module', $provider) . "/templates/{$key}.body";
    if (!empty(glob("$path*"))) {
      return $path;
    }
    return FALSE;
  }

  /**
   * The context to pass to a renderer when rendering the body template.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The message key.
   *
   * @return array
   *   An array of variable names and values.
   */
  protected function getBodyContext(EntityInterface $entity, string $key) {
    $entity_url = $this->getEntityUrlBase() . $entity->toUrl()->toString();
    return [
      $entity->getEntityTypeId() => $entity,
      $entity->getEntityTypeId() . '_url' => $entity_url,
    ];
  }

}
