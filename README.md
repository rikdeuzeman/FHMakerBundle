# FHMakerBundle

Installation
============

The bundle can be installed using Composer. Add the following requirement and repository to composer.json:

```yaml
// composer.json
{
    // ...
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:jeffreymoelands/FHMakerBundle.git"
        }
    ]
}
```

Run Composer to install all required dependencies:

```bash
composer require freshheads/maker-bundle
```

Add the bundle and its dependencies (if not already present) to bundles.php:

```php
// in config/bundles.php
return [
    // ...
    FH\Bundle\MakerBundle\FHMakerBundle::class => ['dev' => true],
    // ...
];
```
