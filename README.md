# Filament Table Filter Presets

[![Latest Version on Packagist](https://img.shields.io/packagist/v/wotz/filament-table-filter-presets.svg?style=flat-square)](https://packagist.org/packages/wotz/filament-table-filter-presets)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/wotz/filament-table-filter-presets/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/wotz/filament-table-filter-presets/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/wotz/filament-table-filter-presets/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/wotz/filament-table-filter-presets/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/wotz/filament-table-filter-presets.svg?style=flat-square)](https://packagist.org/packages/wotz/filament-table-filter-presets)

Add the HasSavedTableFilters trait to any ListRecords page to get Save, Load, and Delete header actions. Filter presets are scoped per user and per resource, stored as JSON, and persisted via a single saved_table_filters table.

## Installation

You can install the package via composer:

```bash
composer require wotz/filament-table-filter-presets
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-table-filter-presets-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-table-filter-presets-config"
```

This is the contents of the published config file:

```php
return [
    'administrator_model' => User::class,
];
```


## Documentation

For the full documentation, check [here](./docs/index.md).

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email info@whoownsthezebra.be instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
