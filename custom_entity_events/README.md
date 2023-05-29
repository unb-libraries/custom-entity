# custom-entity/custom_entity_events
Dispatch and subscribe to events triggered by interacting with custom entity instances, such as creating, updating, or deleting entities.

## Installation
Enable the module by running
```sh
drush en custom_entity_events
```
or via the Drupal UI under ```/admin/modules```.

## Usage
To subscribe to a custom entity event, implement a subclass of ```Drupal\custom_entity_events\EventSubscriber\EntityEventSubscriber```:

```php
class MyEntityEventSubscriber extends EntityEventSubscriber {

  public function doOnEntityCreate(EntityEvent $event) {
    // do something when an entity is created.
  }
  
  public function doOnEntityUpdate(EntityEvent $event) {
    // do something when an entity is updated.
  }
  
  public function doOnEntitySave(EntityEvent $event) {
    // do something when an entity is created or updated.
  }
  
  public function doOnEntityDelete(EntityEvent $event) {
    // do something when an entity is deleted.
  }
}
```

Only implement relevant ```doOnEntity<EVENT_TYPE>``` methods. For example, if you only need to listen when an entity is created, you need to only implement an ```doOnEntityCreate``` handler.

To activate the subscriber, register an EventSubscriber service:
```yaml
my_custom.entity_event_subscriber:
    class: Drupal\my_module\EventSubscriber\MyEntityEventSubscriber
    arguments: [ 'my_entity' ]
    tags:
      - { name: 'event_subscriber' }
```

The service argument is optional. When omitted, the subscriber listens to events triggered by entities of **_any_** type.

## Testing
The module does not provide automated testing. In order to verify its features are working as expected, follow the following steps:

1. Enable the ```custom_entity_examples``` module:

    ```sh
    drush en custom_entity_examples
    ```
   or via the Drupal UI under ```/admin/modules```.

2. Navigate to ```/cex/add``` and add a new blog _"My Blog"_.
3. You should be seeing the message __*An event was fired upon the creation of "My Blog"*__.
