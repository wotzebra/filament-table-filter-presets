<?php

namespace Wotz\FilamentTableFilterPresets;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wotz\FilamentTableFilterPresets\Commands\FilamentTableFilterPresetsCommand;

class FilamentTableFilterPresetsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('filament-table-filter-presets')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_filament_table_filter_presets_table');
    }
}
