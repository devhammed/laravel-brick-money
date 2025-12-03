<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\postJson;

beforeEach(function () {
    Route::post('/test-currency', function (Request $request) {
        return $request->currency('currency');
    });
});

it('supports currency json', function () {
    postJson('/test-currency', ['currency' => 'USD'])
        ->assertSuccessful()
        ->assertJson([
            'name' => 'US Dollar',
            'code' => 'USD',
            'numeric_code' => 840,
            'symbol' => '$',
        ]);
});

it('throws error for invalid currency', function () {
    postJson('/test-currency', ['currency' => 'NOT_VALID'])
        ->assertInternalServerError();
});
