<?php

namespace Drupal\custom_entity_mail\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\custom_entity_events\Event\EntityEvent;
use Drupal\custom_entity_events\EventSubscriber\EntityEventSubscriber;

/**
 * Event subscriber sending emails upon entity events.
 */
abstract class EntityEventMailer extends EntityEventSubscriber {

  /**
   * The mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Get the mail manager service.
   *
   * @return \Drupal\Core\Mail\MailManagerInterface
   *   A mail manager instance.
   */
  protected function mailManager() {
    return $this->mailManager;
  }

  /**
   * Construct an EntityMailEventSubscriber.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   A mail manager instance.
   * @param string $entity_type_id
   *   A string.
   */
  public function __construct(MailManagerInterface $mail_manager, string $entity_type_id) {
    parent::__construct($entity_type_id);
    $this->mailManager = $mail_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function doOnEntityCreate(EntityEvent $event) {
    $entity = $event->getEntity();
    $key = "{$entity->getEntityTypeId()}.created";
    $this->mail(
      $event,
      $key,
      $this->getRecipients($event),
      $this->getSubject($entity, $key),
      $this->getBody($entity, $key)
    );
  }

  /**
   * {@inheritDoc}
   */
  public function doOnEntityUpdate(EntityEvent $event) {
    $entity = $event->getEntity();
    $key = "{$entity->getEntityTypeId()}.updated";
    $this->mail(
      $event,
      $key,
      $this->getRecipients($event),
      $this->getSubject($entity, $key),
      $this->getBody($entity, $key)
    );
  }

  /**
   * {@inheritDoc}
   */
  public function doOnEntityDelete(EntityEvent $event) {
    $entity = $event->getEntity();
    $key = "{$entity->getEntityTypeId()}.deleted";
    $this->mail(
      $event,
      $key,
      $this->getRecipients($event),
      $this->getSubject($entity, $key),
      $this->getBody($entity, $key)
    );
  }

  /**
   * Build the subject content or definition.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The key to determine a template name.
   *
   * @return false|array|string
   *   An array containing a template path and context, if a suitable context
   *   for the given entity and key is available. FALSE if no template can be
   *   found.
   *
   *   Example:
   *   For an entity of type "node" and a template key "created", a suitable
   *   template must be located in the "/templates" folder of the module
   *   defining the "node" entity type. The template name must match the form
   *   "node.created.subject*".
   */
  abstract protected function getSubject(EntityInterface $entity, string $key);

  /**
   * Build the body content or definition.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $key
   *   The key to determine a template name.
   *
   * @return false|array|string
   *   An array containing a template path and context, if a suitable context
   *   for the given entity and key is available. FALSE if no template can be
   *   found.
   *
   *   Example:
   *   For an entity of type "node" and a template key "created", a suitable
   *   template must be located in the "/templates" folder of the module
   *   defining the "node" entity type. The template name must match the form
   *   "node.created.body*".
   */
  abstract protected function getBody(EntityInterface $entity, string $key);

  /**
   * Get the recipients which should be notified.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   The event.
   *
   * @return array
   *   An array of email address strings.
   */
  abstract protected function getRecipients(EntityEvent $event);

  /**
   * Send the mail.
   *
   * @param \Drupal\custom_entity_events\Event\EntityEvent $event
   *   The entity event.
   * @param string $key
   *   A key.
   * @param array $recipients
   *   An array of email addresses.
   * @param string|array $subject
   *   The email subject.
   * @param string|array $body
   *   The email body.
   */
  protected function mail(EntityEvent $event, string $key, array $recipients, $subject = '', $body = '') {
    $entity = $event->getEntity();
    $module = $entity->getEntityType()->getProvider();
    $lang_code = $this->getLangcode($event->getEntity());
    $this->mailManager()->mail($module, $key, implode(',', $recipients), $lang_code, [
      'subject' => $subject,
      'body' => $body,
    ]);
  }

  /**
   * Determine the lang code.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   An entity.
   *
   * @return string
   *   If the entity is translatable and is assigned a language, return that
   *   langcode. Otherwise return a default langcode.
   */
  protected function getLangcode(EntityInterface $entity) {
    try {
      return $entity->get('langcode')
        ->value;
    }
    catch (\InvalidArgumentException $e) {
      // @todo Load the site's default langcode.
      return 'en';
    }
  }

}
