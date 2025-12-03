<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests\Eloquent\Models;

use Devhammed\LaravelBrickMoney\Casts\CurrencyCast;
use Devhammed\LaravelBrickMoney\Casts\DecimalMoneyCast;
use Devhammed\LaravelBrickMoney\Casts\IntegerMoneyCast;
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
        'price' => IntegerMoneyCast::class.':price_currency',
        'price_currency' => CurrencyCast::class,
        'tax' => IntegerMoneyCast::class,
        'gas_fee' => DecimalMoneyCast::class,
        'platform_fee' => DecimalMoneyCast::class.':platform_fee_currency',
        'platform_fee_currency' => CurrencyCast::class,
    ];

    protected static function newFactory(): TransactionModelFactory
    {
        return TransactionModelFactory::new();
    }
}
