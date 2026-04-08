<?php

declare(strict_types=1);

use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Money;

if (! function_exists('money')) {
    function money(
        BigNumber|float|int|string $amount,
        ?string $currency = null,
        ?bool $minor = null,
        ?Context $context = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary
    ): Money {
        if ($currency === null) {
            /** @var string $currency */
            $currency = config('brick-money.currency');
        }

        if ($minor === null) {
            /** @var bool $minor */
            $minor = config('brick-money.minor');
        }

        return $minor
            ? Money::ofMinor($amount, $currency, $context, $roundingMode)
            : Money::of($amount, $currency, $context, $roundingMode);
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
