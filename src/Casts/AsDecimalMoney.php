<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Casts;

use Devhammed\LaravelBrickMoney\Money;

class AsDecimalMoney extends AsMoney
{
    protected function serializeMoney(Money $value): string
    {
        return (string) $value->getAmount();
    }

    protected function deserializeMoney(float|int|string $amount, string $currency): Money
    {
        return Money::of($amount, $currency);
    }
}
