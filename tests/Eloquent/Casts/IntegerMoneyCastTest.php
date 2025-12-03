<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Tests\Eloquent\Models\TransactionModel;

it('can cast to money with separate currency column', function (int $amount, string $currency, float $expected) {
    $model = new TransactionModel([
        'price' => Money::ofMinor($amount, $currency),
    ]);

    expect($model->price)->toBeInstanceOf(Money::class)
        ->and($model->price->getCurrency()->getCode())->toBe($currency)
        ->and($model->price->getAmount()->toFloat())->toBe($expected)
        ->and($model->price_currency->getCode())->toBe($currency);
})->with([
    [0, 'USD', 0.0],
    [1, 'EUR', 0.01],
    [100, 'EUR', 1.0],
    [-100, 'USD', -1.0],
    [1234, 'EUR', 12.34],
    [-1234, 'USD', -12.34],
]);

it('can cast to money with json column', function (int $amount, string $currency, float $expected) {
    $model = new TransactionModel([
        'tax' => Money::ofMinor($amount, $currency),
    ]);

    expect($model->tax)->toBeInstanceOf(Money::class)
        ->and($model->tax->getCurrency()->getCode())->toBe($currency)
        ->and($model->tax->getAmount()->toFloat())->toBe($expected);
})->with([
    [0, 'USD', 0.0],
    [1, 'EUR', 0.01],
    [100, 'EUR', 1.0],
    [-100, 'USD', -1.0],
    [1234, 'EUR', 12.34],
    [-1234, 'USD', -12.34],
]);

it('stores in integer column with separate currency', function (int $amount, string $currency, string $expected) {
    $model = TransactionModel::factory()->create([
        'price' => Money::ofMinor($amount, $currency),
    ]);

    $this->assertDatabaseHas('transactions', [
        'id' => $model->getKey(),
        'price' => $expected,
        'price_currency' => $currency,
    ]);
})->with([
    [0, 'USD', '0'],
    [1, 'EUR', '1'],
    [100, 'EUR', '100'],
    [-100, 'USD', '-100'],
    [1234, 'EUR', '1234'],
    [-1234, 'USD', '-1234'],
]);

it('stores in json column', function (int $amount, string $currency, string $expected) {
    $model = TransactionModel::factory()->create([
        'tax' => Money::ofMinor($amount, $currency),
    ]);

    $this->assertDatabaseHas('transactions', [
        'id' => $model->getKey(),
        'tax->amount' => $expected,
        'tax->currency' => $currency,
    ]);
})->with([
    [0, 'USD', '0'],
    [1, 'EUR', '1'],
    [100, 'EUR', '100'],
    [-100, 'USD', '-100'],
    [1234, 'EUR', '1234'],
    [-1234, 'USD', '-1234'],
]);
