<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Tests\Http\Requests\MoneyFormRequest;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\postJson;

beforeEach(function () {
    Route::post('/test-money-rule', function (MoneyFormRequest $request) {
        return $request->money('price');
    });
});

it('passes validation', function () {
    postJson('/test-money-rule', ['price' => 100])
        ->assertOk();
});

it('validates money', function () {
    postJson('/test-money-rule', ['price' => 'NOT_VALID'])
        ->assertInvalid(['price' => 'The price is not a valid money.']);
});

it('validates min value', function () {
    postJson('/test-money-rule', ['price' => -1])
        ->assertInvalid(['price' => 'The price must be at least 0.']);
});

it('validates max value', function () {
    postJson('/test-money-rule', ['price' => 100000])
        ->assertInvalid(['price' => 'The price must not be greater than 1000.']);
});
