<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Tests\Database\Models\TransactionModel;

it('can cast currency', function (string $currency, int $decimalPlaces, int $numericCode) {
    $model = new TransactionModel([
        'price_currency' => Currency::of($currency),
    ]);

    expect($model->price_currency)->toBeInstanceOf(Currency::class)
        ->and($model->price_currency->getCode())->toBe($currency)
        ->and($model->price_currency->getDecimalPlaces())->toBe($decimalPlaces)
        ->and($model->price_currency->getNumericCode())->toBe($numericCode);
})->with([
    ['USD', 2, 840],
    ['NGN', 2, 566],
    ['GBP', 2, 826],
]);
