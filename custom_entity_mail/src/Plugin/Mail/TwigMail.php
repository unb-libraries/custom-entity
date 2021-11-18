<?php

namespace Drupal\custom_entity_mail\Plugin\Mail;

use Drupal\Core\Mail\Plugin\Mail\PhpMail;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Template\TwigEnvironment;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines the mail backend for intranet applications.
 *
 * @Mail(
 *   id = "twig_mail",
 *   label = @Translation("Twig PHP mailer"),
 *   description = @Translation("Sends the message as plain text, based on Drupal's default mail backend.")
 * )
 */
class TwigMail extends PhpMail implements ContainerFactoryPluginInterface {

  /**
   * The twig environment service.
   *
   * @var \Drupal\Core\Template\TwigEnvironment
   */
  protected $twig;

  /**
   * Get the twig environment.
   *
   * @return \Drupal\Core\Template\TwigEnvironment
   *   A twig environment.
   */
  protected function twig() {
    return $this->twig;
  }

  /**
   * Construct an IntranetMail plugin.
   *
   * @param \Drupal\Core\Template\TwigEnvironment $twig
   *   The Twig environment service.
   */
  public function __construct(TwigEnvironment $twig) {
    parent::__construct();
    $this->twig = $twig;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($container->get('twig'));
  }

  /**
   * {@inheritDoc}
   *
   * @see \Drupal\custom_entity_mail\Plugin\Mail\TwigMail::formatSubject()
   * @see \Drupal\custom_entity_mail\Plugin\Mail\TwigMail::formatBody()
   */
  public function format(array $message) {
    $this->formatSubject($message);
    $this->formatBody($message);
    return parent::format($message);
  }

  /**
   * Format the subject.
   *
   * If a template path and context is given in the message "params", the
   * subject will be rendered using those. Otherwise the subject remains
   * unaltered.
   *
   * Template paths must not contain a ".twig" file extension.
   *
   * @param array $message
   *   The message array.
   */
  protected function formatSubject(array &$message) {
    if (array_key_exists('subject', $message['params'])) {
      if (array_key_exists('template', $message['params']['subject'])) {
        $template_path = $message['params']['subject']['template'];
        $context = array_key_exists('context', $message['params']['subject'])
          ? $message['params']['subject']['context']
          : [];
        if ($subject = $this->tryRender($template_path, $context)) {
          $message['subject'] = $subject;
        }
      }
      unset($message['params']['subject']);
    }
  }

  /**
   * Format the message body.
   *
   * If a template path and context is given in the message "params", the
   * subject will be rendered using those. Otherwise the message body remains
   * unaltered.
   *
   * Template paths must not contain a ".twig" file extension.
   *
   * @param array $message
   *   The message array.
   */
  protected function formatBody(array &$message) {
    if (array_key_exists('body', $message['params'])) {
      if (array_key_exists('template', $message['params']['body'])) {
        $template_path = $message['params']['body']['template'];
        $context = array_key_exists('context', $message['params']['body'])
          ? $message['params']['body']['context']
          : [];
        if ($body = $this->tryRender($template_path, $context)) {
          $message['body'][] = $body;
        }
      }
      unset($message['params']['body']);
    }
  }

  /**
   * Try render the given Twig template with the given context.
   *
   * @param string $template
   *   The path to the template.
   * @param array $context
   *   An array of variable names and values.
   *
   * @return false|string
   *   The rendered content. FALSE if an error occurs during rendering.
   */
  protected function tryRender(string $template, array $context) {
    try {
      return $this->twig()
        ->render($template . '.twig', $context);
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

}
