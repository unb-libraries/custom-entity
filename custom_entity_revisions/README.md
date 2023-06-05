# custom-entity/custom_entity_revisions
Load, list, view, restore revisions of a custom entity type.

## Installation
Enable the module by running
```sh
drush en custom_entity_revisions
```
or via the Drupal UI under ```/admin/modules```.

## Usage

### Add "Revisions" tab
In order to add a **_Revisions_** tab to a custom entity canonical route, implement a custom route provider:

```php
use Drupal\custom_entity\Entity\Routing\HtmlRouteProvider;

class MyHtmlRouteProvider extends HtmlRouteProvider {

  use RevisionsRouteProviderTrait;

  /**
   * {@inheritDoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);
    $routes->addCollection($this->getAllRevisionRoutes($entity_type));
    return $routes;
  }

}
```

Activate the route provider in the entity definition, along with corresponding revision routes:

```php
/**
 * @ContententityType(
 *   id = "my_entity",
 *   ...
 *   handlers = {
 *     ...
 *     "route_provider" = {
 *       "html" = "Drupal\my_module\Entity\Routing\MyHtmlRouteProvider"
 *     },
 *   },
 *   links = {
 *     ...
 *     "revisions" = "/my-entities/{my_entity}/revisions",
 *     "revision" = "/my-entities/{my_entity}/revisions/{my_entity_revision}",
 *   }
 * )
 */
class MyEntity extends ContentEntityBase {
  // ...
}
```

### Restore entity revision
In order to restore a custom entity instance to a previous revision, add the ```revision-restore-form``` link to your entity definition:

```php
/**
 * @ContententityType(
 *   id = "my_entity",
 *   ...
 *   links = {
 *     ...
 *     "revision-restore-form" = "/my-entities/{my_entity}/revisions/{my_entity_revision}/restore",
 *   }
 * )
 */
class MyEntity extends ContentEntityBase {
  // ...
}
```

To render a form link in the **_Operations_** column of a revisions list, make sure the user is assigned the ```restore <ENTITY_TYPE_ID> revisions``` permission.

### Load revision(s)
To programmatically load all or the most recent revision of a custom entity instance, add the ```Drupal\custom_entity_revisions\Entity\EntityRevisionsTrait``` to your entity definition:

```php
use Drupal\custom_entity_revisions\Entity\EntityRevisionsTrait;

class MyEntity extends ContentEntityBase {

  use EntityRevisionsTrait;

  // ...
}
```
Then use the ```getPreviousRevision()``` or ```getRevisions()``` methods:
```php
$my_entity = Drupal::entityTypeManager()->getStorage('my_entity')->load(1);

/** @var \Drupal\Core\Entity\RevisionableInterface $previous */
$previous = $my_entity->getPreviousRevision();

/** @var \Drupal\Core\Entity\RevisionableInterface[] $all */
$all = $my_entity->getRevisions();
```

## Testing
The module does not provide automated testing. In order to verify its features are working as expected, follow the following steps:

1. Enable the ```custom_entity_examples``` module:

    ```sh
    drush en custom_entity_examples
    ```
   or via the Drupal UI under ```/admin/modules```.

2. Navigate to ```/cex/add``` and add a new blog _"My Blog"_.
3. Navigate to ```/cex/1/posts/add``` and a new post.
4. Navigate to ```/cex/1/posts/1``` and click the _"Revisions"_ tab. You should be seeing a list of revisions.




