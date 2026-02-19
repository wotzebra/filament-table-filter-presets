<?php

namespace Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints;

use Filament\QueryBuilder\Constraints\Constraint;
use Filament\Support\Icons\Heroicon;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableIsTrueOperator;

class TranslatableBooleanConstraint extends Constraint
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon(Heroicon::CheckCircle);

        $this->operators([
            TranslatableIsTrueOperator::class,
        ]);
    }
}
