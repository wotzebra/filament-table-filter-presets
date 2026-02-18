<?php

use Filament\Facades\Filament;
use Livewire\Livewire;
use Workbench\App\Filament\Resources\UserResource\Pages\ListUsers;
use Workbench\App\Models\User;
use Wotz\FilamentTableFilterPresets\Models\SavedTableFilter;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->actingAs($this->admin);

    Filament::setServingStatus(true);
});

it('can save a filter preset', function () {
    Livewire::test(ListUsers::class)
        ->callAction('saveFilter', [
            'name' => 'My Test Filter',
            'is_default' => false,
        ])
        ->assertNotified('Filter saved');

    $this->assertDatabaseHas('saved_table_filters', [
        'administrator_id' => $this->admin->id,
        'name' => 'My Test Filter',
        'is_default' => false,
    ]);
});

it('can save a filter preset as default', function () {
    Livewire::test(ListUsers::class)
        ->callAction('saveFilter', [
            'name' => 'Default Filter',
            'is_default' => true,
        ])
        ->assertNotified('Filter saved');

    $this->assertDatabaseHas('saved_table_filters', [
        'administrator_id' => $this->admin->id,
        'name' => 'Default Filter',
        'is_default' => true,
    ]);
});

it('clears previous default when saving a new default filter', function () {
    $existing = SavedTableFilter::factory()->default()->create([
        'administrator_id' => $this->admin->id,
        'resource' => \Workbench\App\Filament\Resources\UserResource::class,
    ]);

    Livewire::test(ListUsers::class)
        ->callAction('saveFilter', [
            'name' => 'New Default',
            'is_default' => true,
        ])
        ->assertNotified('Filter saved');

    expect($existing->fresh()->is_default)->toBeFalse();

    $this->assertDatabaseHas('saved_table_filters', [
        'name' => 'New Default',
        'is_default' => true,
    ]);
});

it('can load a saved filter preset', function () {
    $filterState = ['status' => ['value' => 'active']];

    $savedFilter = SavedTableFilter::factory()->create([
        'administrator_id' => $this->admin->id,
        'resource' => \Workbench\App\Filament\Resources\UserResource::class,
        'filters' => $filterState,
    ]);

    Livewire::test(ListUsers::class)
        ->callAction('loadFilter', [
            'saved_filter_id' => $savedFilter->id,
        ])
        ->assertRedirect();
});

it('can delete a saved filter preset', function () {
    $savedFilter = SavedTableFilter::factory()->create([
        'administrator_id' => $this->admin->id,
        'resource' => \Workbench\App\Filament\Resources\UserResource::class,
    ]);

    Livewire::test(ListUsers::class)
        ->callAction('deleteFilter', [
            'saved_filter_id' => $savedFilter->id,
        ])
        ->assertNotified('Filter deleted');

    $this->assertDatabaseMissing('saved_table_filters', [
        'id' => $savedFilter->id,
    ]);
});

it('auto-applies default filter on mount', function () {
    SavedTableFilter::factory()->default()->create([
        'administrator_id' => $this->admin->id,
        'resource' => \Workbench\App\Filament\Resources\UserResource::class,
        'filters' => ['status' => ['value' => 'active']],
    ]);

    $component = Livewire::test(ListUsers::class);

    $tableFilters = $component->get('tableFilters');

    expect($tableFilters)->toHaveKey('status')
        ->and($tableFilters['status'])->toHaveKey('value');
});

it('scopes filters per user', function () {
    $otherUser = User::factory()->create();

    SavedTableFilter::factory()->create([
        'administrator_id' => $otherUser->id,
        'name' => 'Other User Filter',
        'resource' => \Workbench\App\Filament\Resources\UserResource::class,
    ]);

    $currentUserFilters = SavedTableFilter::query()
        ->where('administrator_id', $this->admin->id)
        ->pluck('name')
        ->toArray();

    expect($currentUserFilters)->not->toContain('Other User Filter');
});

it('enforces unique constraint on name per user and resource', function () {
    SavedTableFilter::factory()->create([
        'administrator_id' => $this->admin->id,
        'name' => 'Duplicate Name',
        'resource' => \Workbench\App\Filament\Resources\UserResource::class,
    ]);

    Livewire::test(ListUsers::class)
        ->callAction('saveFilter', [
            'name' => 'Duplicate Name',
            'is_default' => false,
        ])
        ->assertNotified('Filter saved');

    expect(SavedTableFilter::query()
        ->where('administrator_id', $this->admin->id)
        ->where('name', 'Duplicate Name')
        ->count()
    )->toBe(1);
});

it('belongs to an administrator', function () {
    $savedFilter = SavedTableFilter::factory()->create([
        'administrator_id' => $this->admin->id,
        'resource' => \Workbench\App\Filament\Resources\UserResource::class,
    ]);

    expect($savedFilter->administrator)
        ->toBeInstanceOf(User::class)
        ->id->toBe($this->admin->id);
});
