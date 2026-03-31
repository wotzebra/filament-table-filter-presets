<?php

namespace Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators;

use Filament\QueryBuilder\Constraints\BooleanConstraint\Operators\IsTrueOperator;
use Filament\Schemas\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Concerns\QueriesTranslatableColumn;

class TranslatableIsTrueOperator extends IsTrueOperator
{
    use QueriesTranslatableColumn;

    /**
     * @return array<Component>
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
            $this->getTranslatableColumn($qualifiedColumn),
            ! $this->isInverse(),
        );
    }
}
