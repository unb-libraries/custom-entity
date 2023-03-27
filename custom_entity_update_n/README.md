# custom-entity/custom_entity_update_n
Simplifies common custom entity field data and schema updates.

## Installation
Enable the module by running
```sh
drush en custom_entity_update_n
```
or via the Drupal UI under ```/admin/modules```.

## Introduction
The ```entity.definition_update_manager``` provided by Drupal allows installing, updating, or removing field schema definitions. However, there are limitations when fields contain data.

## Usage
The ```entity_field.data_update.manager``` provides methods to ```set```, ```copy```, ```move```, and ```delete``` field data.

### Example
In order to move (i.e. copy, then delete) field data from the ```field_title``` to the ```title``` field of the ```my_entity``` entity, run the following inside an **_update_N_** hook:
```php
function my_module_update_9001(array &$sandbox) {
  // ...
  $field_data_update_manager = Drupal::service('entity_field.data_update.manager');
  $field_data_update_manager->move("field_title", "title", 'my_entity');
}
```

```set```, ```copy```, and ```delete``` methods can be used accordingly. For details, please refer to the [```Drupal\custom_entity_update_n\Entity\DataUpdateManagerInterface```](src/Entity/DataUpdateManagerInterface.php) interface documentation.

Please note that depending on an entity field's schema, deleting its data can result in either removing a row from a table (in case of many-to-many relationships), or setting all its values to _NULL_.
