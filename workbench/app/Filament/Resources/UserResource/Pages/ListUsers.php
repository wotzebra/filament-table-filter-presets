<?php

namespace Workbench\App\Filament\Resources\UserResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Workbench\App\Filament\Resources\UserResource;
use Wotz\FilamentTableFilterPresets\Concerns\HasSavedTableFilters;

class ListUsers extends ListRecords
{
    use HasSavedTableFilters;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getSavedFilterAction(),
            $this->getLoadFilterAction(),
            $this->getDeleteFilterAction(),
        ];
    }
}
