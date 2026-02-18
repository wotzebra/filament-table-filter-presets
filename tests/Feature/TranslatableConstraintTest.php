<?php

use Workbench\App\Models\User;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableContainsOperator;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableEndsWithOperator;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableEqualsOperator;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableIsTrueOperator;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\Operators\TranslatableStartsWithOperator;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\TranslatableBooleanConstraint;
use Wotz\FilamentTableFilterPresets\QueryBuilder\Constraints\TranslatableTextConstraint;
use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;

beforeEach(function () {
    LocaleCollection::push(new Locale('en'));
    LocaleCollection::push(new Locale('nl'));
});

it('registers the correct operators on TranslatableTextConstraint', function () {
    $constraint = TranslatableTextConstraint::make('title');

    $operators = $constraint->getOperators();

    expect(array_keys($operators))->toBe([
        'contains',
        'startsWith',
        'endsWith',
        'equals',
    ]);
});

it('registers the correct operator on TranslatableBooleanConstraint', function () {
    $constraint = TranslatableBooleanConstraint::make('is_active');

    $operators = $constraint->getOperators();

    expect(array_keys($operators))->toBe([
        'isTrue',
    ]);
});

it('contains operator generates correct SQL', function () {
    $operator = TranslatableContainsOperator::make()
        ->settings(['locale' => 'en', 'text' => 'hello']);

    $query = $operator->apply(User::query(), 'users.title');

    expect($query->toRawSql())
        ->toContain("JSON_UNQUOTE(JSON_EXTRACT(users.title, '$.en'))")
        ->toContain("like '%hello%'");
});

it('contains operator generates inverse SQL', function () {
    $operator = TranslatableContainsOperator::make()
        ->settings(['locale' => 'nl', 'text' => 'hallo'])
        ->inverse();

    $query = $operator->apply(User::query(), 'users.title');

    expect($query->toRawSql())
        ->toContain("JSON_UNQUOTE(JSON_EXTRACT(users.title, '$.nl'))")
        ->toContain('not')
        ->toContain("like '%hallo%'");
});

it('starts with operator generates correct SQL', function () {
    $operator = TranslatableStartsWithOperator::make()
        ->settings(['locale' => 'en', 'text' => 'hello']);

    $query = $operator->apply(User::query(), 'users.title');

    expect($query->toRawSql())
        ->toContain("JSON_UNQUOTE(JSON_EXTRACT(users.title, '$.en'))")
        ->toContain("like 'hello%'");
});

it('ends with operator generates correct SQL', function () {
    $operator = TranslatableEndsWithOperator::make()
        ->settings(['locale' => 'en', 'text' => 'world']);

    $query = $operator->apply(User::query(), 'users.title');

    expect($query->toRawSql())
        ->toContain("JSON_UNQUOTE(JSON_EXTRACT(users.title, '$.en'))")
        ->toContain("like '%world'");
});

it('equals operator generates correct SQL', function () {
    $operator = TranslatableEqualsOperator::make()
        ->settings(['locale' => 'en', 'text' => 'Hello World']);

    $query = $operator->apply(User::query(), 'users.title');

    expect($query->toRawSql())
        ->toContain("JSON_UNQUOTE(JSON_EXTRACT(users.title, '$.en'))")
        ->toContain("'Hello World'");
});

it('equals operator generates inverse SQL', function () {
    $operator = TranslatableEqualsOperator::make()
        ->settings(['locale' => 'nl', 'text' => 'Hallo Wereld'])
        ->inverse();

    $query = $operator->apply(User::query(), 'users.title');

    expect($query->toRawSql())
        ->toContain("JSON_UNQUOTE(JSON_EXTRACT(users.title, '$.nl'))")
        ->toContain('not');
});

it('is true operator generates correct SQL', function () {
    $operator = TranslatableIsTrueOperator::make()
        ->settings(['locale' => 'en']);

    $query = $operator->apply(User::query(), 'users.is_active');

    expect($query->toRawSql())
        ->toContain("JSON_EXTRACT(users.is_active, '$.en')")
        ->toContain('= 1');
});

it('is true operator generates inverse SQL for is false', function () {
    $operator = TranslatableIsTrueOperator::make()
        ->settings(['locale' => 'en'])
        ->inverse();

    $query = $operator->apply(User::query(), 'users.is_active');

    expect($query->toRawSql())
        ->toContain("JSON_EXTRACT(users.is_active, '$.en')")
        ->toContain('= 0');
});

it('text operators include locale form field', function () {
    $operator = TranslatableContainsOperator::make()
        ->settings(['locale' => 'en', 'text' => 'test']);

    $schema = $operator->getFormSchema();

    expect($schema)->toHaveCount(2);
    expect($schema[0]->getName())->toBe('locale');
    expect($schema[1]->getName())->toBe('text');
});

it('boolean operator includes locale form field', function () {
    $operator = TranslatableIsTrueOperator::make()
        ->settings(['locale' => 'en']);

    $schema = $operator->getFormSchema();

    expect($schema)->toHaveCount(1);
    expect($schema[0]->getName())->toBe('locale');
});

it('trims whitespace from text operator input', function () {
    $operator = TranslatableContainsOperator::make()
        ->settings(['locale' => 'en', 'text' => '  hello  ']);

    $query = $operator->apply(User::query(), 'users.title');

    expect($query->toRawSql())
        ->toContain("like '%hello%'")
        ->not->toContain("like '%  hello  %'");
});

it('uses different locale in JSON path', function () {
    $operatorEn = TranslatableEqualsOperator::make()
        ->settings(['locale' => 'en', 'text' => 'Hello']);

    $operatorNl = TranslatableEqualsOperator::make()
        ->settings(['locale' => 'nl', 'text' => 'Hallo']);

    $queryEn = $operatorEn->apply(User::query(), 'users.title');
    $queryNl = $operatorNl->apply(User::query(), 'users.title');

    expect($queryEn->toRawSql())->toContain("'$.en'");
    expect($queryNl->toRawSql())->toContain("'$.nl'");
});
