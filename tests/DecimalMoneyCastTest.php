<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Tests\TransactionModel;

it('can cast to money with separate currency column', function (float $amount, string $currency, int $expected) {
    $model = new TransactionModel([
        'platform_fee' => Money::of($amount, $currency),
    ]);

    expect($model->platform_fee)->toBeInstanceOf(Money::class)
        ->and($model->platform_fee->getCurrency()->getCode())->toBe($currency)
        ->and($model->platform_fee->getMinorAmount()->toInt())->toBe($expected)
        ->and($model->platform_fee_currency->getCode())->toBe($currency);
})->with([
    [0.0, 'USD', 0],
    [0.01, 'EUR', 1],
    [1, 'EUR', 100],
    [-1, 'USD', -100],
    [12.34, 'EUR', 1234],
    [-12.34, 'USD', -1234],
]);

it('can cast to money with json column', function (float $amount, string $currency, int $expected) {
    $model = new TransactionModel([
        'gas_fee' => Money::of($amount, $currency),
    ]);

    expect($model->gas_fee)->toBeInstanceOf(Money::class)
        ->and($model->gas_fee->getCurrency()->getCode())->toBe($currency)
        ->and($model->gas_fee->getMinorAmount()->toInt())->toBe($expected);
})->with([
    [0.0, 'USD', 0],
    [0.01, 'EUR', 1],
    [1, 'EUR', 100],
    [-1, 'USD', -100],
    [12.34, 'EUR', 1234],
    [-12.34, 'USD', -1234],
]);

it('stores in decimal column with separate currency', function (float $amount, string $currency, string $expected) {
    $model = TransactionModel::factory()->create([
        'platform_fee' => Money::of($amount, $currency),
    ]);

    $this->assertDatabaseHas('transactions', [
        'id' => $model->getKey(),
        'platform_fee' => $expected,
        'platform_fee_currency' => $currency,
    ]);
})->with([
    [0.0, 'USD', '0.00'],
    [0.01, 'EUR', '0.01'],
    [1, 'EUR', '1.00'],
    [-1, 'USD', '-1.00'],
    [12.34, 'EUR', '12.34'],
    [-12.34, 'USD', '-12.34'],
]);

it('stores in json column', function (float $amount, string $currency, string $expected) {
    $model = TransactionModel::factory()->create([
        'gas_fee' => Money::of($amount, $currency),
    ]);

    $this->assertDatabaseHas('transactions', [
        'id' => $model->getKey(),
        'gas_fee->amount' => $expected,
        'gas_fee->currency' => $currency,
    ]);
})->with([
    [0.0, 'USD', '0.00'],
    [0.01, 'EUR', '0.01'],
    [1, 'EUR', '1.00'],
    [-1, 'USD', '-1.00'],
    [12.34, 'EUR', '12.34'],
    [-12.34, 'USD', '-12.34'],
]);
