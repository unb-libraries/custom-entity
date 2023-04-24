<?php

namespace Drupal\custom_entity_examples\Entity;

use Drupal\Core\Entity\ContentEntityBase;

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
 *       "default" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider"
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
 *     "canonical" = "/custom-entity/examples/posts/{cex_post}",
 *     "add-form" = "/custom-entity/examples/posts/add",
 *     "edit-form" = "/custom-entity/examples/posts/{cex_post}/edit",
 *     "delete-form" = "/custom-entity/examples/posts/{cex_post}/delete",
 *     "revisions" = "/custom-entity/examples/posts/{cex_post}/revisions",
 *     "revision" = "/custom-entity/examples/posts/{cex_post}/revisions/{cex_post_revision}",
 *     "revision-restore-form" = "/custom-entity/examples/posts/{cex_post}/revisions/{cex_post_revision}/restore",
 *   },
 *   field_ui_base_route = "entity.cex_post.settings",
 * )
 */
class Post extends ContentEntityBase {

}
