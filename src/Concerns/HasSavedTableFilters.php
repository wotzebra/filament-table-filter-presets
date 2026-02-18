<?php

namespace Wotz\FilamentTableFilterPresets\Concerns;

use Filament\Actions\Action;
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

    protected function getSavedFilterAction(): Action
    {
        return Action::make('saveFilter')
            ->label('Save Filter')
            ->form([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_default')
                    ->label('Set as default'),
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
                    ->title('Filter saved')
                    ->success()
                    ->send();
            });
    }

    protected function getLoadFilterAction(): Action
    {
        return Action::make('loadFilter')
            ->label('Load Filter')
            ->form([
                Select::make('saved_filter_id')
                    ->label('Saved Filter')
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
            ->label('Delete Filter')
            ->color('danger')
            ->form([
                Select::make('saved_filter_id')
                    ->label('Saved Filter')
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
                    ->title('Filter deleted')
                    ->success()
                    ->send();
            });
    }
}
