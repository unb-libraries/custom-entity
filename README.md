# unb-libraries/custom-entity [![GitHub license](https://img.shields.io/github/license/unb-libraries/docker-einbaum)](https://github.com/unb-libraries/lib.unb.ca/blob/prod/LICENSE)
A Drupal module improving the developer experience of working with **_custom entities_**. 

## Installation
The module is a composer package hosted on packagist and can be installed by running
```sh
composer require unb-libraries/custom-entity
```

or by adding it to your project's ```composer.json``` file:

```json
{
  "require": {
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
The module enhances Drupal by providing implementations of:

### Access handlers
The ```Drupal\custom_entity\Entity\Access\EntityAccessControlHandler``` dynamically grants or denies access to an entity whether a user has permission of the form ```<OPERATION> <ENTITY_TYPE> entities```.

For example, in order for a user to get permission to ```edit``` a ```node``` entity, the user would need to have the ```edit node entities``` permission assigned to them.

In order to activate the access control handler, set it in an entity's type definition:

```php
<?php

use Drupal\Core\Entity\ContentEntityBase;

/**
 * Defines the "my_entity" entity type.
 *
 * @ContentEntityType(
 *   ...
 *   handlers = {
 *     ...
 *     "access" = "Drupal\custom_entity\Entity\Access\EntityAccessControlHandler"
 *   },
 *   ...
 * ) 
 */
 class MyEntity extends ContentEntityBase {
   // ...
 }
```

Currently bundle-level permission checking is not supported by the handler.

### Route handlers
The ```Drupal\custom_entity\Entity\Routing\HtmlRouteProvider``` enhances Drupal default HtmlRouteProvider by providing the following additional routes for any entity type that activates it:
- ```entity.my_entity.delete-all```: creates a form route to delete ALL instances of an ```my_entity```, if according ```delete-all``` link and form handler are defined.
- ```entity.my_entity.<FORM_HANDLER>```: creates a form route for a given form handler, if ```my_entity``` defines the according link.

The following defines an entity with ```entity.my_entity.delete-all``` and ```entity.my_entity.activate``` routes: 

```php
<?php

use Drupal\Core\Entity\ContentEntityBase;

/**
 * Defines the "my_entity" entity type.
 *
 * @ContentEntityType(
 *   ...
 *   handlers = {
 *     ...
 *     "form" = {
 *       "activate" = ...
 *     "routing" = "Drupal\custom_entity\Entity\Routing\HtmlRouteProvider"
 *   },
 *   ...
 *   links = {
  *    "activate": "/my-entities/{my_entity}/activate",
 *     "delete-all-form": "/my-entities/delete-all",
 *   }
 * ) 
 */
class MyEntity extends ContentEntityBase {
  // ...
}
```

### Field types
The following new entity field types are provided:

#### Creator
This field type adds a reference to the user who initially created it to an entity. Add this field to an entity as follows:

```php
<?php

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\custom_entity\Entity\UserCreatedInterface;
use Drupal\custom_entity\Entity\EntityCreatedTrait;

/**
 * Defines the "my_entity" entity type.
 *
 * @ContentEntityType(
 *   ...
 * )
 */
class MyEntity extends ContentEntityBase implements UserCreatedInterface {
 
  use EntityCreatedTrait;
   
  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    
    $fields[static::FIELD_CREATOR] = static::getCreatorBaseFieldDefinition($entity_type);
    
    return $fields;
  }
}
```

#### Editor
This field type adds a reference to the user who most recently edited an entity. Add this field to an entity as follows:

```php
<?php

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\custom_entity\Entity\UserEditedInterface;
use Drupal\custom_entity\Entity\EntityChangedTrait;

/**
 * Defines the "my_entity" entity type.
 *
 * @ContentEntityType(
 *   ...
 * )
 */
class MyEntity extends ContentEntityBase implements UserEditedInterface {
 
   use EntityChangedTrait;
   
   /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);
    
    $fields[static::FIELD_CREATOR] = static::getEditorBaseFieldDefinition($entity_type);
    
    return $fields;
  }
 }
```

### Forms
The following base forms are provided:

#### ConfigEntityForm
Base form for config entities that comes with mandatory ```id``` and ```label``` fields pre-configured. Use it as follows:

```php
<?php

use Drupal\custom_entity\Form\ConfigEntityForm;

/**
 * Form for "my_config_entity" entities. 
 */
class MyConfigEntityForm extends ConfigEntityForm {
   
   /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    
    // add additional form fields here.
    
    return $form;
  }
 }
```

#### ConfigEntityBundleForm
Base form for config entities that are bundles of content entities. Built on top of ```ConfigEntityForm```, it allows for additional configuration of a [bundle class](https://www.drupal.org/node/3191609).

### Services

### Submodules

More features can be added by activating any of the following sub-modules:

| Module                  | Description                                                                                                    |
|-------------------------|----------------------------------------------------------------------------------------------------------------|
| [custom_entity_alias](custom_entity_alias)         | Creates path aliases for views related to custom entities.                |
| [custom_entity_events](custom_entity_events/README.md)       | Dispatch and subscribe to custom entity events.                           |
| [custom_entity_mail](custom_entity_mail/README.md)           | Send emails upon creating, editing, deleting custom entities.             |
| [custom_entity_revisions](custom_entity_revisions/README.md) | A collection of components to ease working with custom entity revisions.  |
| [custom_entity_ui](custom_entity_ui/README.md)               | Enhances FieldUI to provide an administration UI for custom entity types. |
| [custom_entity_update_n](custom_entity_update_n/README.md)   | Provides services to simplify custom entity update N hooks.               |

