<?php

namespace Drupal\custom_entity_examples\Entity;

use Drupal\Core\Entity\ContentEntityBase;

/**
 * The "Blog" entity.
 *
 * @ContententityType(
 *   id = "cex_blog",
 *   label = @Translation("Blog"),
 *   label_plural = @Translation("Blogs"),
 *   label_collection = @Translation("Blogs"),
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
 *   base_table = "cex_blog",
 *   admin_permission = "administer cex_blog entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "canonical" = "/cex/{cex_blog}",
 *     "add-form" = "/cex/{cex_blog}/add",
 *     "edit-form" = "/cex/{cex_blog}/edit",
 *     "delete-form" = "/cex/{cex_blog}/delete",
 *   },
 *   field_ui_base_route = "entity.cex_blog.settings",
 * )
 */
class Blog extends ContentEntityBase {

}
