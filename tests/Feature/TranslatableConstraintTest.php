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

    User::create(['name' => 'Alice', 'email' => 'alice@example.com', 'password' => 'secret', 'title' => ['en' => 'Hello World', 'nl' => 'Hallo Wereld'], 'is_active' => ['en' => true, 'nl' => false]]);
    User::create(['name' => 'Bob', 'email' => 'bob@example.com', 'password' => 'secret', 'title' => ['en' => 'Goodbye Moon', 'nl' => 'Tot ziens Maan'], 'is_active' => ['en' => false, 'nl' => true]]);
    User::create(['name' => 'Charlie', 'email' => 'charlie@example.com', 'password' => 'secret', 'title' => ['en' => 'Hello There', 'nl' => 'Hallo Daar'], 'is_active' => ['en' => true, 'nl' => true]]);
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

it('contains operator filters matching records', function () {
    $operator = TranslatableContainsOperator::make()
        ->settings(['locale' => 'en', 'text' => 'Hello']);

    $results = $operator->apply(User::query(), 'title')->pluck('name')->all();

    expect($results)->toBe(['Alice', 'Charlie']);
});

it('contains operator inverse excludes matching records', function () {
    $operator = TranslatableContainsOperator::make()
        ->settings(['locale' => 'en', 'text' => 'Hello'])
        ->inverse();

    $results = $operator->apply(User::query(), 'title')->pluck('name')->all();

    expect($results)->toBe(['Bob']);
});

it('starts with operator filters matching records', function () {
    $operator = TranslatableStartsWithOperator::make()
        ->settings(['locale' => 'nl', 'text' => 'Hallo']);

    $results = $operator->apply(User::query(), 'title')->pluck('name')->all();

    expect($results)->toBe(['Alice', 'Charlie']);
});

it('ends with operator filters matching records', function () {
    $operator = TranslatableEndsWithOperator::make()
        ->settings(['locale' => 'en', 'text' => 'Moon']);

    $results = $operator->apply(User::query(), 'title')->pluck('name')->all();

    expect($results)->toBe(['Bob']);
});

it('equals operator filters exact match', function () {
    $operator = TranslatableEqualsOperator::make()
        ->settings(['locale' => 'en', 'text' => 'Hello World']);

    $results = $operator->apply(User::query(), 'title')->pluck('name')->all();

    expect($results)->toBe(['Alice']);
});

it('equals operator inverse excludes exact match', function () {
    $operator = TranslatableEqualsOperator::make()
        ->settings(['locale' => 'en', 'text' => 'Hello World'])
        ->inverse();

    $results = $operator->apply(User::query(), 'title')->pluck('name')->all();

    expect($results)->toBe(['Bob', 'Charlie']);
});

it('is true operator filters truthy records', function () {
    $operator = TranslatableIsTrueOperator::make()
        ->settings(['locale' => 'en']);

    $results = $operator->apply(User::query(), 'is_active')->pluck('name')->all();

    expect($results)->toBe(['Alice', 'Charlie']);
});

it('is true operator inverse filters falsy records', function () {
    $operator = TranslatableIsTrueOperator::make()
        ->settings(['locale' => 'en'])
        ->inverse();

    $results = $operator->apply(User::query(), 'is_active')->pluck('name')->all();

    expect($results)->toBe(['Bob']);
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
        ->settings(['locale' => 'en', 'text' => '  Hello  ']);

    $results = $operator->apply(User::query(), 'title')->pluck('name')->all();

    expect($results)->toBe(['Alice', 'Charlie']);
});

it('queries the correct locale', function () {
    $operatorEn = TranslatableIsTrueOperator::make()
        ->settings(['locale' => 'en']);

    $operatorNl = TranslatableIsTrueOperator::make()
        ->settings(['locale' => 'nl']);

    $resultsEn = $operatorEn->apply(User::query(), 'is_active')->pluck('name')->all();
    $resultsNl = $operatorNl->apply(User::query(), 'is_active')->pluck('name')->all();

    expect($resultsEn)->toBe(['Alice', 'Charlie']);
    expect($resultsNl)->toBe(['Bob', 'Charlie']);
});
