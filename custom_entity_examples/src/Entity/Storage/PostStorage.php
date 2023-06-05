<?php

namespace Drupal\custom_entity_examples\Entity\Storage;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageInterface;
use Drupal\custom_entity_revisions\Entity\Storage\RevisionableEntityStorageTrait;

/**
 * Storage handler for "cex_post" entities.
 */
class PostStorage extends SqlContentEntityStorage implements RevisionableEntityStorageInterface {

  use RevisionableEntityStorageTrait;

}
