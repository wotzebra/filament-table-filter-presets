<?php

namespace Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints;

use Filament\QueryBuilder\Constraints\Constraint;
use Filament\Support\Icons\Heroicon;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableContainsOperator;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableEndsWithOperator;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableEqualsOperator;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableStartsWithOperator;

class TranslatableTextConstraint extends Constraint
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::Language);

        $this->operators([
            TranslatableContainsOperator::class,
            TranslatableStartsWithOperator::class,
            TranslatableEndsWithOperator::class,
            TranslatableEqualsOperator::class,
        ]);
    }
}
