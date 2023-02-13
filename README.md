# unb-libraries/custom-entity [![GitHub license](https://img.shields.io/github/license/unb-libraries/docker-einbaum)](https://github.com/unb-libraries/lib.unb.ca/blob/prod/LICENSE)
A Drupal module improving the developer experience of working with **_custom entities_**. 

## Installation
The module is a composer package hosted on packagist and can be installed by running
```sh
composer require unb-libraries/custom-entity
```

or by adding it to your project's composer file:

```json
{
  // ... 
  "require": {
    // ...
    "unb-libraries/custom-entity": "dev-9.x-1.x"
  }
}
```
Enable the module by running
```sh
drush en custom_entity
```

or via the Drupal UI under ```/admin/modules```.

## Usage
The module enhances Drupal by providing implementations of the following kinds:

### Access handlers

### Route handlers

### Field types

### Forms

### Services

### Submodules

More features can be added by activating any of the following sub-modules:

| Module                  | Description                                                                                                    |
|-------------------------|----------------------------------------------------------------------------------------------------------------|
| [custom_entity_alias](custom_entity_alias/README.md)         | Creates path aliases for views related to custom entities.                |
| [custom_entity_events](custom_entity_events/README.md)       | Dispatch and subscribe to custom entity events.                           |
| [custom_entity_mail](custom_entity_mail/README.md)           | Send emails upon creating, editing, deleting custom entities.             |
| [custom_entity_revisions](custom_entity_revisions/README.md) | A collection of components to ease working with custom entity revisions.  |
| [custom_entity_ui](custom_entity_ui/README.md)               | Enhances FieldUI to provide an administration UI for custom entity types. |
| [custom_entity_update_n](custom_entity_update_n/README.md)   | Provides services to simplify custom entity update N hooks.               |

