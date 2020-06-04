Installation instructions
=========================

## Use Composer

Run the following from your website root folder to install NetgenEzFormsBundle:

```bash
$ composer require netgen/ez-forms-bundle
```

### Activate the bundle

Activate the bundle in `config/bundles.php` file.

```php
<?php

return [
    ...,

    Netgen\Bundle\EzFormsBundle\NetgenEzFormsBundle::class => ['all' => true],

    ...
];
```

## Clear the caches

Clear the eZ Platform caches with the following command:

```bash
$ php bin/console cache:clear
```
