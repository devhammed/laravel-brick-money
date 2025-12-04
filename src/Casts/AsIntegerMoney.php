<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Casts;

use Devhammed\LaravelBrickMoney\Concerns\AsMoneyCast;
use Devhammed\LaravelBrickMoney\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @template-implements CastsAttributes<Money,Money>
 */
class AsIntegerMoney implements CastsAttributes
{
    use AsMoneyCast;

    protected function serializeMoney(Money $value): string
    {
        return (string) $value->getMinorAmount();
    }

    protected function deserializeMoney(float|int|string $amount, string $currency): Money
    {
        return Money::ofMinor($amount, $currency);
    }
}
