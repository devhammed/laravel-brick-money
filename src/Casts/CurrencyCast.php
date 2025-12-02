<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Casts;

use Brick\Money\Exception\UnknownCurrencyException;
use Devhammed\LaravelBrickMoney\Currency;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @template-implements CastsAttributes<Currency,Currency>
 */
class CurrencyCast implements CastsAttributes
{
    /**
     * Transform the attribute from the underlying model values.
     */
    public function get($model, string $key, mixed $value, array $attributes): Currency
    {
        if (! is_string($value)) {
            throw new UnknownCurrencyException(__('brick-money::validation.unexpected_value', [
                'expected' => 'string',
                'actual' => gettype($value),
            ]));
        }

        return Currency::of($value);
    }

    /**
     * Transform the attribute to its underlying model values.
     */
    public function set($model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof Currency) {
            throw new UnknownCurrencyException(__('brick-money::validation.unexpected_value', [
                'expected' => Currency::class,
                'actual' => gettype($value),
            ]));
        }

        return $value->getCode();
    }
}
