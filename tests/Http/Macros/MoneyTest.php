<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\postJson;

beforeEach(function () {
    Route::post('/test-major-money', function (Request $request) {
        return $request->money('price');
    });

    Route::post('/test-minor-money', function (Request $request) {
        return $request->money('price', minor: true);
    });

    Route::post('/test-default-money', function (Request $request) {
        return $request->money('price', 0);
    });
});

it('supports string money json in major unit', function () {
    postJson('/test-major-money', ['price' => '100'])
        ->assertSuccessful()
        ->assertJson([
            'amount' => '100.00',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);
});

it('supports int money json in major unit', function () {
    postJson('/test-major-money', ['price' => 100])
        ->assertSuccessful()
        ->assertJson([
            'amount' => '100.00',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);
});

it('supports float money json in major unit', function () {
    postJson('/test-major-money', ['price' => 100.50])
        ->assertSuccessful()
        ->assertJson([
            'amount' => '100.50',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);
});

it('supports string money json in minor unit', function () {
    postJson('/test-minor-money', ['price' => '100'])
        ->assertSuccessful()
        ->assertJson([
            'amount' => '1.00',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);
});

it('supports int money json in minor unit', function () {
    postJson('/test-minor-money', ['price' => 100])
        ->assertSuccessful()
        ->assertJson([
            'amount' => '1.00',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);
});

it('does not supports float money json in minor unit', function () {
    postJson('/test-minor-money', ['price' => 100.50])
        ->assertInternalServerError();
});

it('supports money json default', function () {
    postJson('/test-default-money')
        ->assertSuccessful()
        ->assertJson([
            'amount' => '0',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);
});

it('does not support invalid values', function () {
    postJson('/test-default-money', ['price' => 'NOT_VALID'])
        ->assertInternalServerError();
});
