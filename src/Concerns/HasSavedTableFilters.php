<?php

namespace Wotz\FilamentTableFilterPresets\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Wotz\FilamentTableFilterPresets\Models\SavedTableFilter;

trait HasSavedTableFilters
{
    public function mountHasSavedTableFilters(): void
    {
        $default = $this->getDefaultSavedFilter();

        if ($default) {
            $this->tableFilters = $default->filters;
        }
    }

    protected function getResourceClass(): string
    {
        return static::$resource;
    }

    protected function getDefaultSavedFilter(): ?SavedTableFilter
    {
        return SavedTableFilter::query()
            ->where('administrator_id', Filament::auth()->id())
            ->where('resource', $this->getResourceClass())
            ->where('is_default', true)
            ->first();
    }

    /**
     * Apply the saved filter state by persisting to session and navigating,
     * so the component re-mounts with properly hydrated RuleBuilder schemas.
     * Uses Livewire's SPA navigation for a seamless experience.
     *
     * @param  array<string, mixed>  $filters
     */
    protected function applySavedFilterState(array $filters): void
    {
        session()->put(
            $this->getTableFiltersSessionKey(),
            $filters,
        );

        $this->redirect(static::getUrl(), navigate: true);
    }

    protected function getFilterActionsGroup(): ActionGroup
    {
        return ActionGroup::make([
            $this->getSavedFilterAction(),
            $this->getLoadFilterAction(),
            $this->getDeleteFilterAction(),
        ])
            ->button()
            ->label(__('filament-table-filter-presets::filters.actions'))
            ->icon('heroicon-m-ellipsis-vertical');
    }

    protected function getSavedFilterAction(): Action
    {
        return Action::make('saveFilter')
            ->label(__('filament-table-filter-presets::filters.save'))
            ->form([
                TextInput::make('name')
                    ->label(__('filament-table-filter-presets::filters.name'))
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_default')
                    ->label(__('filament-table-filter-presets::filters.set_as_default')),
            ])
            ->action(function (array $data): void {
                $resource = $this->getResourceClass();

                if ($data['is_default']) {
                    SavedTableFilter::query()
                        ->where('administrator_id', auth()->id())
                        ->where('resource', $resource)
                        ->where('is_default', true)
                        ->update(['is_default' => false]);
                }

                SavedTableFilter::query()->updateOrCreate(
                    [
                        'administrator_id' => auth()->id(),
                        'resource' => $resource,
                        'name' => $data['name'],
                    ],
                    [
                        'filters' => $this->tableFilters ?? [],
                        'is_default' => $data['is_default'],
                    ],
                );

                Notification::make()
                    ->title(__('filament-table-filter-presets::filters.filter_saved'))
                    ->success()
                    ->send();
            });
    }

    protected function getLoadFilterAction(): Action
    {
        return Action::make('loadFilter')
            ->label(__('filament-table-filter-presets::filters.load'))
            ->form([
                Select::make('saved_filter_id')
                    ->label(__('filament-table-filter-presets::filters.saved_filter'))
                    ->options(fn () => SavedTableFilter::query()
                        ->where('administrator_id', auth()->id())
                        ->where('resource', $this->getResourceClass())
                        ->pluck('name', 'id'))
                    ->required(),
            ])
            ->action(function (array $data): void {
                $filter = SavedTableFilter::query()
                    ->where('administrator_id', auth()->id())
                    ->where('id', $data['saved_filter_id'])
                    ->first();

                if ($filter) {
                    $this->applySavedFilterState($filter->filters);
                }
            });
    }

    protected function getDeleteFilterAction(): Action
    {
        return Action::make('deleteFilter')
            ->label(__('filament-table-filter-presets::filters.delete'))
            ->color('danger')
            ->form([
                Select::make('saved_filter_id')
                    ->label(__('filament-table-filter-presets::filters.saved_filter'))
                    ->options(fn () => SavedTableFilter::query()
                        ->where('administrator_id', auth()->id())
                        ->where('resource', $this->getResourceClass())
                        ->pluck('name', 'id'))
                    ->required(),
            ])
            ->requiresConfirmation()
            ->action(function (array $data): void {
                SavedTableFilter::query()
                    ->where('administrator_id', auth()->id())
                    ->where('id', $data['saved_filter_id'])
                    ->delete();

                Notification::make()
                    ->title(__('filament-table-filter-presets::filters.filter_deleted'))
                    ->success()
                    ->send();
            });
    }
}
