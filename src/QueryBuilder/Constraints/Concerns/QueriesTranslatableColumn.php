<?php

namespace Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Concerns;

use Filament\Forms\Components\Select;
use Wotz\LocaleCollection\Facades\LocaleCollection;

trait QueriesTranslatableColumn
{
    protected function getLocaleFormField(): Select
    {
        $locales = LocaleCollection::toBase()->map->locale()->toArray();

        return Select::make('locale')
            ->label(__('Locale'))
            ->options(array_combine($locales, $locales))
            ->default(app()->getLocale())
            ->required();
    }

    protected function getTranslatableColumn(string $qualifiedColumn): string
    {
        $locale = $this->getSettings()['locale'];

        return "{$qualifiedColumn}->{$locale}";
    }
}
