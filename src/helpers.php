<?php

declare(strict_types=1);

use Brick\Math\BigNumber;
use Brick\Money\Context;
use Brick\Math\RoundingMode;
use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Money;

if (! function_exists('money')) {
    function money(
        BigNumber|float|int|string $amount,
        ?string $currency = null,
        ?bool $major = null,
        ?Context $context = null,
        RoundingMode $roundingMode = RoundingMode::UNNECESSARY
    ): Money {
        if ($currency === null) {
            /** @var string $currency */
            $currency = config('brick-money.currency');
        }

        if ($major === null) {
            /** @var bool $major */
            $major = config('brick-money.major');
        }

        return $major
            ? Money::of($amount, $currency, $context, $roundingMode)
            : Money::ofMinor($amount, $currency, $context, $roundingMode);
    }
}

if (! function_exists('currency')) {
    function currency(?string $currency = null): Currency
    {
        if ($currency === null) {
            /** @var string $currency */
            $currency = config('brick-money.currency');
        }

        return Currency::of($currency);
    }
}
