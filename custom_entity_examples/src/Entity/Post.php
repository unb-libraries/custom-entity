<?php

namespace Drupal\custom_entity_examples\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;

/**
 * The "Post" entity.
 *
 * @ContententityType(
 *   id = "cex_post",
 *   label = @Translation("Post"),
 *   label_plural = @Translation("Posts"),
 *   label_collection = @Translation("Posts"),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\custom_entity_examples\Form\PostForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity_examples\Entity\Routing\PostHtmlRouteProvider"
 *     },
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler"
 *   },
 *   base_table = "cex_post",
 *   revision_table = "cex_post_revision",
 *   admin_permission = "administer cex_post entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "rid",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/cex/{cex_blog}/{cex_post}",
 *     "add-form" = "/cex/{cex_blog}/add",
 *     "edit-form" = "/cex/{cex_blog}/{cex_post}/edit",
 *     "delete-form" = "/cex/{cex_blog}/{cex_post}/delete",
 *     "revisions" = "/cex/{cex_blog}/{cex_post}/revisions",
 *     "revision" = "/cex/{cex_blog}/{cex_post}/revisions/{cex_post_revision}",
 *     "revision-restore-form" = "/cex/{cex_blog}/posts/{cex_post}/revisions/{cex_post_revision}/restore",
 *   },
 *   field_ui_base_route = "entity.cex_post.settings",
 * )
 */
class Post extends ContentEntityBase {

  /**
   * Gets the blog this post belongs to.
   *
   * @return \Drupal\custom_entity_examples\Entity\Blog
   *   The blog this post belongs to.
   */
  public function getBlog() {
    return $this->get('blog')->entity;
  }

  /**
   * Sets the blog this post belongs to.
   *
   * @param \Drupal\custom_entity_examples\Entity\Blog $blog
   *   The blog this post belongs to.
   *
   * @return $this
   */
  public function setBlog(Blog $blog) {
    $this->set('blog', $blog);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function urlRouteParameters($rel) {
    $parameters = parent::urlRouteParameters($rel);
    $parameters['cex_blog'] = $this->getBlog()->id();
    return $parameters;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['blog'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Blog'))
      ->setDescription(t('The blog this post belongs to.'))
      ->setRequired(TRUE)
      ->setRevisionable(FALSE)
      ->setSetting('target_type', 'cex_blog')
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', FALSE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    if (!$this->get('blog')->entity) {
      throw new MissingMandatoryParametersException('Blog is required.');
    }
    parent::preSave($storage);
  }

}
