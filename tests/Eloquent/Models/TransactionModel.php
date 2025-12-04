<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests\Eloquent\Models;

use Devhammed\LaravelBrickMoney\Casts\AsCurrency;
use Devhammed\LaravelBrickMoney\Casts\AsDecimalMoney;
use Devhammed\LaravelBrickMoney\Casts\AsIntegerMoney;
use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Tests\Eloquent\Factories\TransactionModelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Money $tax
 * @property Money $price
 * @property Currency $price_currency
 * @property Money $gas_fee
 * @property Money $platform_fee
 * @property Currency $platform_fee_currency
 */
class TransactionModel extends Model
{
    /** @use HasFactory<TransactionModelFactory> */
    use HasFactory;

    protected $table = 'transactions';

    protected $casts = [
        'price' => AsIntegerMoney::class.':price_currency',
        'price_currency' => AsCurrency::class,
        'tax' => AsIntegerMoney::class,
        'gas_fee' => AsDecimalMoney::class,
        'platform_fee' => AsDecimalMoney::class.':platform_fee_currency',
        'platform_fee_currency' => AsCurrency::class,
    ];

    protected static function newFactory(): TransactionModelFactory
    {
        return TransactionModelFactory::new();
    }
}
