<?php

namespace Wotz\FilamentTableFilterPresets\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Wotz\FilamentTableFilterPresets\Models\SavedTableFilter;

class SavedTableFilterFactory extends Factory
{
    protected $model = SavedTableFilter::class;

    public function definition(): array
    {
        $administratorModel = config('filament-table-filter-presets.administrator_model');

        return [
            'administrator_id' => $administratorModel::factory(),
            'resource' => null,
            'name' => fake()->unique()->words(2, true),
            'filters' => [],
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
