Installation instructions
=========================

## Use Composer

Run the following from your website root folder to install NetgenEzFormsBundle:

```bash
$ composer require netgen/ez-forms-bundle
```

## Activate the bundle

Activate required bundles in `app/AppKernel.php` file by adding them to the `$bundles` array in `registerBundles` method:

```php
public function registerBundles()
{
    ...
    $bundles[] = new Netgen\Bundle\EzFormsBundle\NetgenEzFormsBundle();

    return $bundles;
}
```

## Clear the caches

Clear the eZ Platform caches with the following command:

```bash
$ php bin/console cache:clear
```
