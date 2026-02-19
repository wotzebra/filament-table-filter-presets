<?php

namespace Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators;

use Filament\Forms\Components\TextInput;
use Filament\QueryBuilder\Constraints\TextConstraint\Operators\StartsWithOperator;
use Illuminate\Database\Eloquent\Builder;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Concerns\QueriesTranslatableColumn;

class TranslatableStartsWithOperator extends StartsWithOperator
{
    use QueriesTranslatableColumn;

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    public function getFormSchema(): array
    {
        return [
            $this->getLocaleFormField(),
            TextInput::make('text')
                ->label(__('filament-query-builder::query-builder.operators.text.form.text.label'))
                ->required(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        $text = trim($this->getSettings()['text']);

        return $query->{$this->isInverse() ? 'whereNotLike' : 'whereLike'}(
            $this->getTranslatableColumn($qualifiedColumn),
            "{$text}%",
        );
    }
}
