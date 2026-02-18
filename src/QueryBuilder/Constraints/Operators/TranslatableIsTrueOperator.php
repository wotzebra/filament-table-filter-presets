<?php

namespace Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators;

use Filament\QueryBuilder\Constraints\BooleanConstraint\Operators\IsTrueOperator;
use Illuminate\Database\Eloquent\Builder;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Concerns\QueriesTranslatableColumn;

class TranslatableIsTrueOperator extends IsTrueOperator
{
    use QueriesTranslatableColumn;

    /**
     * @return array<\Filament\Schemas\Components\Component>
     */
    public function getFormSchema(): array
    {
        return [
            $this->getLocaleFormField(),
        ];
    }

    public function apply(Builder $query, string $qualifiedColumn): Builder
    {
        return $query->where(
            $this->getTranslatableBooleanExpression($qualifiedColumn),
            ! $this->isInverse(),
        );
    }
}
