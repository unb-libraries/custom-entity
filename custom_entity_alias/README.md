# custom-entity/custom_entity_alias
Creates an alias for a view path that includes a contextual filter on a custom entity type.

## Introduction
```my_category``` be a custom entity type that groups many ```my_entity``` entities. Their canonical URL paths be ```/my-categories/{my_category}``` and ```/my-categories/{my_category}/my-entities/{my_entity}``` respectively.

Path-auto patterns for both entity types ensure that both ```/my-categories/1``` ```/my-categories/my-category``` as well as ```/my-categories/my-category/my-entities/1``` and ```/my-categories/my-category/my-entiites/my-entity``` deliver equal results.

```my_entities_view``` be a view listing all ```my_entity``` members of a given ```my_category``` under ```/my-categories/%my_category/my-entities```.

Because the ``pathauto`` module does not handle two patterns for the same entity type correctly, a list of all members of **_My category_** can only be viewed under ```/my-categories/1/my-entities```.

The ```custom_entity_alias``` solves that problem.

## Installation
Enable the module by running
```sh
drush en custom_entity_alias
```
or via the Drupal UI under ```/admin/modules```.

## Usage
The module provides the ```views.alias_generator``` service that creates path aliases for views with contextual filters on entity types. To generate a new alias, e.g. each time a new ```my_category``` entities is created, call the service from within a ```hook_ENTITY_TYPE_insert``` implementation:

```php
function my_module_my_category_insert(\Drupal\Core\Entity\EntityInterface $entity) {
  $view = \Drupal::entityTypeManager()
    ->getStorage('view')
    ->load('my_entities_view');
    
  Drupal::service('views.alias_generator')
    ->generateViewAlias($view, $entity);
}
```

## Configuration
The ```generateViewAlias``` method accepts an ```options``` array that allows customizing the exact alias being generated. Refer to the [EntityViewAliasGenerator](./src/views/EntityViewAliasGenerator.php) implementation for details.

## Testing
The module does not provide automated testing. In order to verify its features are working as expected, follow the following steps: 

1. Enable the ```custom_entity_examples``` module:

    ```sh
    drush en custom_entity_examples
    ```
    or via the Drupal UI under ```/admin/modules```.

2. Navigate to ```/cex/add``` and add a new blog _"My Blog"_.
3. Navigate to ```/cex/my-blog/posts```. You should be seeing an empty posts page.




