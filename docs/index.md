# Filament Table Filter Presets

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

## Usage

Add the `HasSavedTableFilters` trait to your Filament `ListRecords` page and register the header actions:

```php
use Wotz\FilamentTableFilterPresets\Concerns\HasSavedTableFilters;

class ListOrders extends ListRecords
{
    use HasSavedTableFilters;

    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getSavedFilterAction(),
            $this->getLoadFilterAction(),
            $this->getDeleteFilterAction(),
        ];
    }
}
```

This gives you three header actions:

- **Save Filter** — Saves the current table filter state under a name. You can optionally mark it as the default preset.
- **Load Filter** — Shows a dropdown of your saved presets and applies the selected one.
- **Delete Filter** — Shows a dropdown of your saved presets and deletes the selected one (with confirmation).

### Default filters

When saving a filter you can toggle "Set as default". A default filter is automatically applied when the page loads. Each user can have one default filter per resource.

### How filters are scoped

Filter presets are scoped by the authenticated user (`administrator_id`) and the Filament resource class (`resource`). This means each user has their own independent set of presets per resource.

### Database schema

The package creates a `saved_table_filters` table with the following columns:

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `administrator_id` | foreignId | The user who owns the preset |
| `resource` | string | The Filament resource class |
| `name` | string | The preset name |
| `filters` | json | The serialized filter state |
| `is_default` | boolean | Whether this is the default preset |

A unique constraint on `(administrator_id, resource, name)` ensures preset names are unique per user per resource. Saving with an existing name will update the existing preset.

### Configuration

The `administrator_model` config value determines which model the `administrator_id` foreign key references. By default, this is `Illuminate\Foundation\Auth\User`. If your application uses a custom user model, update the config:

```php
// config/filament-table-filter-presets.php
return [
    'administrator_model' => App\Models\Administrator::class,
];
```

## Laravel Boost Skill

This package ships with a [Laravel Boost](https://laravel.com/docs/boost) skill called `filament-table-filter-presets`. It automates the setup of QueryBuilder constraints and the `HasSavedTableFilters` trait for a Filament resource.

### What it does

1. Generates the `QueryBuilder` constraints for your resource's table filters.
2. Adds the `HasSavedTableFilters` trait to the `ListRecords` page of the given resource.

### When to use it

Use this skill when you are adding filter presets to a Filament resource and want to scaffold the QueryBuilder configuration and trait setup automatically, instead of doing it manually.
