<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney;

use Brick\Money\Currency as BrickCurrency;
use Brick\Money\Exception\UnknownCurrencyException;
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
     * Create an instance of Currency.
     */
    private function __construct(string $currency)
    {
        $currency = mb_strtoupper(mb_trim($currency));
        $currencies = static::getCurrencies();

        if (! array_key_exists($currency, $currencies)) {
            throw new UnknownCurrencyException(__('brick-money::validation.invalid_currency'));
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
     * Set the available currencies.
     *
     * @param  array<mixed>  $currencies
     */
    public static function setCurrencies(array $currencies): void
    {
        static::$currencies = $currencies;
    }

    /**
     * Get the available currencies.
     *
     * @return array<mixed>
     */
    public static function getCurrencies(): array
    {
        if (! isset(static::$currencies)) {
            $config = require __DIR__.'/../config/brick-money.php';

            static::$currencies = $config['currencies'];
        }

        return static::$currencies;
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
     * Get the currency thousand separator.
     */
    public function getThousandSeparator(): string
    {
        return $this->thousandSeparator;
    }

    /**
     * Get the amount prefix.
     */
    public function getPrefix(): string
    {
        if (! $this->isSymbolFirst()) {
            return '';
        }

        return $this->getSymbol().($this->isSymbolSpaced() ? ' ' : '');
    }

    /**
     * Get the amount suffix.
     */
    public function getSuffix(): string
    {
        if ($this->isSymbolFirst()) {
            return '';
        }

        return ($this->isSymbolSpaced() ? ' ' : '').$this->getSymbol();
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
        return [
            'name' => $this->getName(),
            'code' => $this->getCode(),
            'numeric_code' => $this->getNumericCode(),
            'symbol' => $this->getSymbol(),
            'symbol_first' => $this->isSymbolFirst(),
            'symbol_spaced' => $this->isSymbolSpaced(),
            'decimal_places' => $this->getDecimalPlaces(),
            'decimal_separator' => $this->getDecimalSeparator(),
            'thousand_separator' => $this->getThousandSeparator(),
            'prefix' => $this->getPrefix(),
            'suffix' => $this->getSuffix(),
        ];
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
