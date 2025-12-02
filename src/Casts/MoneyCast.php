<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Casts;

use Brick\Money\Exception\MoneyMismatchException;
use Devhammed\LaravelBrickMoney\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @template-implements CastsAttributes<Money,Money>
 */
abstract class MoneyCast implements CastsAttributes
{
    /**
     * The currency column.
     */
    protected ?string $currencyColumn = null;

    /**
     * The amount column.
     */
    protected ?string $amountColumn = null;

    /**
     * Create instance of the cast.
     */
    public function __construct(?string $currency = null, ?string $amount = null)
    {
        $this->currencyColumn = $currency;

        $this->amountColumn = $amount;
    }

    /**
     * Transform the attribute from the underlying model values.
     *
     * @return array<string,mixed>|Money
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Money|array
    {
        $extractedValue = $this->usesJson()
            ? json_decode(is_string($value) ? $value : '{}', true)
            : $attributes;

        $amountColumn = $this->usesJson() ? 'amount' : ($this->amountColumn ?? $key);

        $currencyColumn = $this->usesJson() ? 'currency' : $this->currencyColumn;

        if (! is_array($extractedValue)) {
            throw new MoneyMismatchException(__('brick-money::validation.unexpected_value', [
                'expected' => 'array',
                'actual' => gettype($value),
            ]));
        }

        $amount = $extractedValue[$amountColumn] ?? null;

        if (! is_string($amount) && ! is_int($amount) && ! is_float($amount)) {
            throw new MoneyMismatchException(__('brick-money::validation.unexpected_value', [
                'expected' => 'string, int, float',
                'actual' => gettype($amount),
            ]));
        }

        $currency = $extractedValue[$currencyColumn] ?? null;

        if (! is_string($currency)) {
            throw new MoneyMismatchException(__('brick-money::validation.unexpected_value', [
                'expected' => 'string',
                'actual' => gettype($currency),
            ]));
        }

        $money = $this->dehydrate($amount, $currency);

        if ($this->usesJson()) {
            return $money;
        }

        return [
            $this->amountColumn ?? $key => $money,
            $this->currencyColumn => $money->getCurrency(),
        ];
    }

    /**
     * Transform the attribute to its underlying model values.
     *
     * @return array<string,mixed>|string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string|array
    {
        if (! $value instanceof Money) {
            throw new MoneyMismatchException(__('brick-money::validation.unexpected_value', [
                'expected' => Money::class,
                'actual' => gettype($value),
            ]));
        }

        $amount = $this->hydrate($value);

        $currency = (string) $value->getCurrency();

        if ($this->usesJson()) {
            return (string) json_encode([
                'amount' => $amount,
                'currency' => $currency,
            ]);
        }

        return [
            $this->amountColumn ?? $key => $amount,
            $this->currencyColumn => $currency,
        ];
    }

    /**
     * Determine if cast should be JSON.
     */
    protected function usesJson(): bool
    {
        return $this->currencyColumn === null;
    }

    /**
     * Serialize the given value.
     */
    abstract protected function hydrate(Money $value): string;

    /**
     * Deserialize the given value.
     */
    abstract protected function dehydrate(string|int|float $amount, string $currency): Money;
}
