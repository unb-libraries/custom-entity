# custom-entity/custom_entity_ui
Enhances FieldUI to provide an administration UI for custom entity types.

## Installation
Enable the module by running
```sh
drush en custom_entity_ui
```
or via the Drupal UI under ```/admin/modules```.

## Usage
In order to use the following features in the Drupal UI, add a ```field_ui_base_route``` to the end of a custom entity definition:

```php
/**
 * The "My Entity" entity.
 *
 * @ContententityType(
 *   id = "my_entity",
 *   ...
 *   field_ui_base_route = "entity.my_entity.settings",
 * )
 */
class MyEntity extends ContentEntityBase {
  // ...
}
```

The ```entity.<ENTITY_TYPE_ID>.settings``` route is provided by the module. If your entity type has bundles, set ```field_ui_base_route``` to the entity bundle type's edit form, e.g. ```entity.my_entity_type.edit_form```.

Once set, your entity type will be listed under ```/admin/structure/custom-types```, enabling quick access to Managing fields, forms, and displays.

### Managing permissions
If your entity type includes bundle, you may enable a _Manage permissions_ tab by including a permissions form entry in your link templates:

```php
/**
 * @ContententityType(
 *   id = "my_entity",
 *   ...
 *   links = (
 *     ...
 *     "entity-permissions-form" = "/admin/structure/my-module/manage/{my_entity_type}/permissions",
 *   ),
 * )
 */
```

### Advanced form settings
The module allows for additional form display configuration:

- **_Redirect_**: provide a (parameterized) route to which the user will be redirected upon successful form submission
- **_Success message_**: provide a (parameterized) message which will be displayed to the user upon successful form submission


