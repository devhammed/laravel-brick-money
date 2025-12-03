<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Tests\Http\Requests\CurrencyFormRequest;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\postJson;

beforeEach(function () {
    Route::post('/test-currency-rule', function (CurrencyFormRequest $request) {
        return $request->currency('currency');
    });
});

it('passes validation', function () {
    postJson('/test-currency-rule', ['currency' => 'USD'])
        ->assertOk();
});

it('rejects invalid', function () {
    postJson('/test-currency-rule', ['currency' => 'NOT_VALID'])
        ->assertInvalid([
            'currency' => 'The selected currency is invalid.',
        ]);
});
