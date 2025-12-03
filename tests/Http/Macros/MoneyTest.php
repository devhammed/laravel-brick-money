<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\postJson;

it('supports money json in major unit', function () {
    Route::post('/test-money', function (Request $request) {
        return $request->money('price');
    });

    postJson('/test-money', ['price' => '100'])
        ->assertSuccessful()
        ->assertJson([
            'amount' => '10000',
            'value' => '100.00',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);

    postJson('/test-money', ['price' => 100])
        ->assertSuccessful()
        ->assertJson([
            'amount' => '10000',
            'value' => '100.00',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);
});

it('supports money json in minor unit', function () {
    Route::post('/test-money', function (Request $request) {
        return $request->money('price', minor: true);
    });

    postJson('/test-money', ['price' => '100'])
        ->assertSuccessful()
        ->assertJson([
            'amount' => '100',
            'value' => '1.00',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);

    postJson('/test-money', ['price' => 100])
        ->assertSuccessful()
        ->assertJson([
            'amount' => '100',
            'value' => '1.00',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);
});

it('supports money json default', function () {
    Route::post('/test-money', function (Request $request) {
        return $request->money('price', 0);
    });

    postJson('/test-money')
        ->assertSuccessful()
        ->assertJson([
            'amount' => '0',
            'value' => '0',
            'currency' => [
                'name' => 'US Dollar',
                'code' => 'USD',
                'numeric_code' => 840,
                'symbol' => '$',
            ],
        ]);
});

it('throws an exception when money is not valid', function () {
    Route::post('/test-money', function (Request $request) {
        return $request->money('price');
    });

    postJson('/test-money', ['price' => 'NOT_VALID'])
        ->assertInternalServerError();
});
