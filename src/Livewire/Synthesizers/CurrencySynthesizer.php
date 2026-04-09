<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Livewire\Synthesizers;

use Brick\Money\Exception\UnknownCurrencyException;
use Devhammed\LaravelBrickMoney\Currency;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class CurrencySynthesizer extends Synth
{
    public static string $key = 'currency';

    public static function match(mixed $target): bool
    {
        return $target instanceof Currency;
    }

    /** @return array{string, array<mixed>} */
    public function dehydrate(Currency $target): array
    {
        return [$target->getCode(), []];
    }

    public function hydrate(string $value): Currency
    {
        return Currency::of($value);
    }

    public function get(&$target, $key): string|int|bool
    {
        if (! $target instanceof Currency) {
            throw new UnknownCurrencyException(__('brick-money::validation.unexpected_value', [
                'expected' => Currency::class,
                'actual' => gettype($target),
            ]));
        }

        return match ($key) {
            'name' => $target->getName(),
            'code' => $target->getCode(),
            'numeric_code' => $target->getNumericCode(),
            'symbol' => $target->getSymbol(),
            'symbol_first' => $target->isSymbolFirst(),
            'symbol_spaced' => $target->isSymbolSpaced(),
            'decimal_places' => $target->getDecimalPlaces(),
            'decimal_separator' => $target->getDecimalSeparator(),
            'thousand_separator' => $target->getThousandSeparator(),
            default => throw new UnknownCurrencyException(__('brick-money::validation.invalid_selection', [
                'attribute' => 'property',
            ])),
        };
    }

    public function set(&$target, $key, $value): void
    {
        if (! $target instanceof Currency) {
            throw new UnknownCurrencyException(__('brick-money::validation.unexpected_value', [
                'expected' => Currency::class,
                'actual' => gettype($target),
            ]));
        }

        if ($key === 'code') {
            $target = Currency::of($value);
        } else {
            throw new UnknownCurrencyException(__('brick-money::validation.invalid_selection', [
                'attribute' => 'property',
            ]));
        }
    }
}
