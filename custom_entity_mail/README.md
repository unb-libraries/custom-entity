# custom-entity/custom_entity_mail
Send emails upon creating, editing, deleting custom entities.

## Installation
Enable the module by running
```sh
drush en custom_entity_mail
```
or via the Drupal UI under ```/admin/modules```.

## Usage
The module allows you to register event subscribers to send email notifications upon entity events:

```php
my_custom_mailer.entity_event_subscriber:
    class: Drupal\my_module\EventSubscriber\MyCustomMailer
    arguments: [ 'my_entity']
    parent: template_mailer.entity_event_subscriber
    tags:
      - { name: 'event_subscriber' }
```

In its most simple form, an event subscriber inherits from ```EntityEventTemplateMailer``` and overrides the ```getSubscribedEventNames()``` and ```getRecipients()``` methods:

```php
class MyCustomMailer extends EntityEventTemplateMailer {
  
  /**
   * {@inheritDoc}
   */
  protected static function getSubscribedEventNames() {
    return [
      Drupal\custom_entity_events\Event\EntityEvents::CREATE,
    ];
  }
  
  /**
   * {@inheritDoc}
   */
  protected function getRecipients(EntityEvent $event) {
    // Assume entity has a 'field_email' field.
    $email = $event->getEntity()->get('field_email')->value;
    return [$email];
  }
}
```

By default the event subscriber will look for subject and body templates of the form ```ENTITY_TYPE_ID.EVENT_TYPE.body|subject```. For example, to send an email confirmation upon creation of a ```my_entity``` entity, provide subject and body templates, e.g.

**_my_module/templates/my_entity.create.subject.twig_**:
```twig
{{ my_entity.label }} has been created
```

**_my_module/templates/my_entity.create.body.twig_**:
```twig
Hi,

This is a confirmation that you created {{ my_entity.label }}.

Thank you!
```

To customize the template context and/or filename, override the ```get<Subject|Body>Context()``` and/or ```get<Subject|Body>Template()``` methods. Refer to [```Drupal\custom_entity_mail\EventSubscriber\EntityEventTemplateMailer```](src/EventSubscriber/EntityEventTemplateMailer.php) for more details.
