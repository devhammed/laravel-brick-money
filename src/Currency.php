<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney;

use Brick\Money\Currency as BrickCurrency;
use Brick\Money\Exception\UnknownCurrencyException;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Stringable;

/**
 * Currency Class.
 *
 * @template-implements Arrayable<string,array>
 */
class Currency implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    use Macroable;

    /**
     * The code.
     */
    protected string $code;

    /**
     * The name.
     */
    protected string $name;

    /**
     * The numeric code.
     */
    protected int $numericCode;

    /**
     * The symbol.
     */
    protected string $symbol;

    /**
     * Does symbol comes first?
     */
    protected bool $symbolFirst;

    /**
     * Is symbol and amount spaced?
     */
    protected bool $symbolSpaced;

    /**
     * The decimal places.
     */
    protected int $decimalPlaces;

    /**
     * The decimal separator.
     */
    protected string $decimalSeparator;

    /**
     * The thousand places.
     */
    protected int $thousandPlaces;

    /**
     * The thousand separator.
     */
    protected string $thousandSeparator;

    /**
     * The `Brick\Money\Currency` instance.
     */
    protected BrickCurrency $currency;

    /**
     * The available currencies.
     *
     * @var array<mixed>
     */
    protected static array $currencies;

    /**
     * The callback to use for JSON serialization.
     */
    protected static Closure $jsonSerializer;

    /**
     * Create an instance of Currency.
     */
    private function __construct(string $currency)
    {
        $currency = mb_strtoupper(mb_trim($currency));
        $currencies = static::currencies();

        if (! array_key_exists($currency, $currencies)) {
            throw UnknownCurrencyException::unknownCurrency($currency);
        }

        $attributes = (array) $currencies[$currency];
        $this->code = $currency;
        $this->name = (string) $attributes['name'];
        $this->numericCode = (int) $attributes['numeric_code'];
        $this->symbol = (string) $attributes['symbol'];
        $this->symbolFirst = (bool) $attributes['symbol_first'];
        $this->symbolSpaced = (bool) $attributes['symbol_spaced'];
        $this->decimalPlaces = (int) $attributes['decimal_places'];
        $this->decimalSeparator = (string) $attributes['decimal_separator'];
        $this->thousandPlaces = (int) $attributes['thousand_places'];
        $this->thousandSeparator = (string) $attributes['thousand_separator'];
        $this->currency = new BrickCurrency(
            $this->code,
            $this->numericCode,
            $this->name,
            $this->decimalPlaces,
        );
    }

    /**
     * Create an instance of `Currency` from `Brick\Money\Currency` or `string`.
     */
    public static function of(BrickCurrency|string $currency): static
    {
        if ($currency instanceof BrickCurrency) {
            $currency = $currency->getCurrencyCode();
        }

        return new static($currency);
    }

    /**
     * Get or set the available currencies.
     *
     * @param  array<mixed>|null  $currencies
     * @return array<mixed>
     */
    public static function currencies(?array $currencies = null): array
    {
        if ($currencies !== null) {
            static::$currencies = $currencies;
        }

        if (! isset(static::$currencies)) {
            $config = require __DIR__.'/../config/brick-money.php';

            static::$currencies = $config['currencies'];
        }

        return static::$currencies;
    }

    /**
     * Get or set the callback to use for JSON serialization.
     */
    public static function jsonSerializer(?Closure $callback = null): Closure
    {
        if ($callback !== null) {
            static::$jsonSerializer = $callback;
        }

        return static::$jsonSerializer ??= fn (Currency $currency) => [
            'name' => $currency->getName(),
            'code' => $currency->getCode(),
            'numeric_code' => $currency->getNumericCode(),
            'symbol' => $currency->getSymbol(),
            'symbol_first' => $currency->isSymbolFirst(),
            'symbol_spaced' => $currency->isSymbolSpaced(),
            'decimal_places' => $currency->getDecimalPlaces(),
            'decimal_separator' => $currency->getDecimalSeparator(),
            'thousand_places' => $currency->getThousandPlaces(),
            'thousand_separator' => $currency->getThousandSeparator(),
        ];
    }

    /**
     * Get the currency code.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Get the currency name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the currency numeric code (ISO).
     */
    public function getNumericCode(): int
    {
        return $this->numericCode;
    }

    /**
     * Get the currency symbol.
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * Determine if the currency symbol comes first during formatting.
     */
    public function isSymbolFirst(): bool
    {
        return $this->symbolFirst;
    }

    /**
     * Determine if the currency symbol should have space before/after the amount.
     */
    public function isSymbolSpaced(): bool
    {
        return $this->symbolSpaced;
    }

    /**
     * Get the currency decimal places.
     */
    public function getDecimalPlaces(): int
    {
        return $this->decimalPlaces;
    }

    /**
     * Get the currency decimal separator.
     */
    public function getDecimalSeparator(): string
    {
        return $this->decimalSeparator;
    }

    /**
     * Get the currency thousand places.
     */
    public function getThousandPlaces(): int
    {
        return $this->thousandPlaces;
    }

    /**
     * Get the currency thousand separator.
     */
    public function getThousandSeparator(): string
    {
        return $this->thousandSeparator;
    }

    /**
     * Get the `Brick\Money\Currency` instance.
     */
    public function getCurrency(): BrickCurrency
    {
        return $this->currency;
    }

    /**
     * Returns whether this currency is equal to the given currency.
     * The currencies are considered equal if their currency codes are equal.
     */
    public function is(Currency $currency): bool
    {
        return $this->getCurrency()->is($currency->getCurrency());
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return static::jsonSerializer()($this);
    }

    /**
     * Convert the object to its JSON representation.
     */
    public function toJson($options = 0): string
    {
        return (string) json_encode($this->toArray(), $options);
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its string representation.
     */
    public function __toString(): string
    {
        return $this->getCode();
    }
}
