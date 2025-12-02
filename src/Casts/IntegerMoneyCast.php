<?php

namespace Devhammed\LaravelBrickMoney\Casts;

use Devhammed\LaravelBrickMoney\Money;

class IntegerMoneyCast extends MoneyCast
{
    protected function hydrate(Money $value): string
    {
        return (string) $value->getMinorAmount();
    }

    protected function dehydrate(float|int|string $amount, string $currency): Money
    {
        return Money::ofMinor($amount, $currency);
    }
}
