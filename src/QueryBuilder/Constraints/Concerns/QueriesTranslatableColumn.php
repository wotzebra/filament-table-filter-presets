<?php

namespace Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Concerns;

use Filament\Forms\Components\Select;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
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

    protected function getTranslatableExpression(string $qualifiedColumn): Expression
    {
        $locale = $this->getSettings()['locale'];

        return DB::raw("JSON_UNQUOTE(JSON_EXTRACT({$qualifiedColumn}, '$.{$locale}'))");
    }

    protected function getTranslatableBooleanExpression(string $qualifiedColumn): Expression
    {
        $locale = $this->getSettings()['locale'];

        return DB::raw("JSON_EXTRACT({$qualifiedColumn}, '$.{$locale}')");
    }
}
