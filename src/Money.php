<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney;

use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Exception\MoneyMismatchException;
use Brick\Money\Money as BrickMoney;
use Brick\Money\MoneyContainer;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use NumberFormatter;
use Stringable;

/**
 * Money Class.
 *
 * @template-implements Arrayable<string,string|Currency>
 */
class Money implements Arrayable, Jsonable, JsonSerializable, MoneyContainer, Stringable
{
    use Macroable;

    /**
     * The `Brick\Money\Money` instance.
     */
    protected BrickMoney $money;

    /**
     * The currency.
     */
    protected Currency $currency;

    /**
     * The locale to use for formatting.
     */
    protected static string $locale;

    /**
     * The callback to use for JSON serialization.
     */
    protected static Closure $jsonSerializer;

    /**
     * Whether to use minor units when converting to JSON.
     */
    protected static bool $jsonSerializeMinorUnits;

    /**
     * Create an instance of Money.
     */
    private function __construct(
        BrickMoney $money,
        Currency $currency,
    ) {
        $this->money = $money;

        $this->currency = $currency;
    }

    /**
     * Returns Money of the given amount and currency.
     *
     * By default, the money is created with a `DefaultContext`. This means that the amount is scaled to match the currency's default fraction digits. For example, `Money::of('2.5', 'USD')` will yield `USD 2.50`. If the amount cannot be safely converted to this scale, an exception is thrown.
     *
     * To override this behaviour, a Context instance can be provided. Operations on this Money return a Money with the same context.
     */
    public static function of(
        BigNumber|float|int|string $amount,
        Currency|string|int $currency,
        ?Context $context = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary
    ): static {
        if (! $currency instanceof Currency) {
            $currency = Currency::of($currency);
        }

        $money = BrickMoney::of($amount, $currency->getCurrency(), $context, $roundingMode);

        return new static($money, $currency);
    }

    /**
     * Returns Money from a number of minor units.
     *
     * By default, the money is created with a `DefaultContext`. This means that the amount is scaled to match the currency's default fraction digits. For example, `Money::ofMinor('1234', 'USD')` will yield `USD 12.34`. If the amount cannot be safely converted to this scale, an exception is thrown.
     */
    public static function ofMinor(
        BigNumber|float|int|string $amount,
        Currency|string|int $currency,
        ?Context $context = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary
    ): static {
        if (! $currency instanceof Currency) {
            $currency = Currency::of($currency);
        }

        $money = BrickMoney::ofMinor($amount, $currency->getCurrency(), $context, $roundingMode);

        return new static($money, $currency);
    }

    /**
     * Create an instance of `Money` from `Brick\Money\Money`.
     */
    public static function ofMoney(BrickMoney $money): static
    {
        return new static($money, Currency::of($money->getCurrency()));
    }

    /**
     * Returns the minimum of the given monies.
     *
     * If several monies are equal to the minimum value, the first one is returned.
     *
     * @param  Money  $money  The first money.
     * @param  Money  ...$monies  The subsequent monies.
     *
     * @throws MoneyMismatchException If all the monies are not in the same currency.
     */
    public static function min(Money $money, Money ...$monies): Money
    {
        $min = $money;

        foreach ($monies as $money) {
            if ($money->isLessThan($min)) {
                $min = $money;
            }
        }

        return $min;
    }

    /**
     * Returns the maximum of the given monies.
     *
     * If several monies are equal to the maximum value, the first one is returned.
     *
     * @param  Money  $money  The first money.
     * @param  Money  ...$monies  The subsequent monies.
     *
     * @throws MoneyMismatchException If all the monies are not in the same currency.
     */
    public static function max(Money $money, Money ...$monies): Money
    {
        $max = $money;

        foreach ($monies as $money) {
            if ($money->isGreaterThan($max)) {
                $max = $money;
            }
        }

        return $max;
    }

    /**
     * Returns the total of the given monies.
     *
     * The monies must share the same currency and context.
     *
     * @param  Money  $money  The first money.
     * @param  Money  ...$monies  The subsequent monies.
     *
     * @throws MoneyMismatchException If all the monies are not in the same currency and context.
     */
    public static function total(Money $money, Money ...$monies): Money
    {
        $total = $money;

        foreach ($monies as $money) {
            $total = $total->plus($money);
        }

        return $total;
    }

    /**
     * Returns the average of the given monies.
     *
     * The monies must share the same currency and context.
     *
     * @param  Money  $money  The first money.
     * @param  Money  ...$monies  The subsequent monies.
     *
     * @throws MoneyMismatchException If all the monies are not in the same currency and context.
     */
    public static function avg(Money $money, Money ...$monies): Money
    {
        $total = static::total($money, ...$monies);

        $count = 1 + count($monies);

        return $total->dividedBy($count);
    }

    /**
     * Returns a Money with zero value, in the given currency.
     *
     * By default, the money is created with a DefaultContext: it has the default scale for the currency.
     * A Context instance can be provided to override the default.
     *
     * @param  Currency|string|int  $currency  The Currency instance, ISO currency code or ISO numeric currency code.
     * @param  Context|null  $context  An optional context.
     */
    public static function zero(Currency|string|int $currency, ?Context $context = null): Money
    {
        return static::of(0, $currency, $context);
    }

    /**
     * Get or set the locale to use for formatting.
     */
    public static function locale(?string $locale = null): string
    {
        if ($locale !== null) {
            static::$locale = str_replace('-', '_', $locale);
        }

        return static::$locale ??= 'en_US';
    }

    /**
     * Get or set the callback to use for JSON serialization.
     */
    public static function jsonSerializer(?Closure $callback = null): Closure
    {
        if ($callback !== null) {
            static::$jsonSerializer = $callback;
        }

        return static::$jsonSerializer ??= fn (Money $money) => [
            'amount' => (string) (static::jsonSerializeMinorUnits() ? $money->getMinorAmount() : $money->getAmount()),
            'currency' => $money->getCurrency(),
        ];
    }

    /**
     * Get or set whether to use minor units when converting to JSON.
     */
    public static function jsonSerializeMinorUnits(?bool $value = null): bool
    {
        if ($value !== null) {
            static::$jsonSerializeMinorUnits = $value;
        }

        return static::$jsonSerializeMinorUnits ??= false;
    }

    /**
     * Get the `Brick\Money\Money` instance.
     */
    public function getMoney(): BrickMoney
    {
        return $this->money;
    }

    /**
     * Returns the Currency of this Money.
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * Returns the amounts contained in this money container, indexed by currency code.
     *
     * @return BigNumber[]
     *
     * @psalm-return array<string, BigNumber>
     */
    public function getAmounts(): array
    {
        return [
            $this->getCurrency()->getCode() => $this->getAmount(),
        ];
    }

    /**
     * Returns the Context of this Money.
     */
    public function getContext(): Context
    {
        return $this->getMoney()->getContext();
    }

    /**
     * Returns the amount of this Money, as a BigDecimal.
     */
    public function getAmount(): BigDecimal
    {
        return $this->getMoney()->getAmount();
    }

    /**
     * Returns the amount of this Money in minor units (cents) for the currency.
     *
     * The value is returned as a BigDecimal. If this Money has a scale greater than that of the currency, the result
     * will have a non-zero scale.
     *
     * For example, `USD 1.23` will return a BigDecimal of `123`, while `USD 1.2345` will return `123.45`.
     */
    public function getMinorAmount(): BigDecimal
    {
        return $this->getMoney()->getMinorAmount();
    }

    /**
     * Returns a BigInteger containing the unscaled value (all digits) of this money.
     *
     * For example, `123.4567 USD` will return a BigInteger of `1234567`.
     */
    public function getUnscaledAmount(): BigInteger
    {
        return $this->getMoney()->getUnscaledAmount();
    }

    /**
     * Returns the sign of this number.
     *
     * Returns -1 if the number is negative, 0 if zero, 1 if positive.
     */
    public function getSign(): int
    {
        return $this->getMoney()->getSign();
    }

    /**
     * Returns whether this money has zero value.
     */
    public function isZero(): bool
    {
        return $this->getMoney()->isZero();
    }

    /**
     * Checks if this number is strictly negative.
     */
    public function isNegative(): bool
    {
        return $this->getMoney()->isNegative();
    }

    /**
     * Checks if this number is negative or zero.
     */
    public function isNegativeOrZero(): bool
    {
        return $this->getMoney()->isNegativeOrZero();
    }

    /**
     * Checks if this number is strictly positive.
     */
    public function isPositive(): bool
    {
        return $this->getMoney()->isPositive();
    }

    /**
     * Checks if this number is positive or zero.
     */
    public function isPositiveOrZero(): bool
    {
        return $this->getMoney()->isPositiveOrZero();
    }

    /**
     * Compares this number to the given one.
     *
     * Returns -1 if `$this` is lower than, 0 if equal to, 1 if greater than `$that`.
     */
    public function compareTo(Money|BigNumber|int|float|string $that): int
    {
        return $this->getMoney()->compareTo($this->getAmountOf($that));
    }

    /**
     * Checks if this number is equal to the given one.
     */
    public function isEqualTo(Money|BigNumber|int|float|string $that): bool
    {
        return $this->getMoney()->isEqualTo($this->getAmountOf($that));
    }

    /**
     * Checks if this number is strictly lower than the given one.
     */
    public function isLessThan(Money|BigNumber|int|float|string $that): bool
    {
        return $this->getMoney()->isLessThan($this->getAmountOf($that));
    }

    /**
     * Checks if this number is lower than or equal to the given one.
     */
    public function isLessThanOrEqualTo(Money|BigNumber|int|float|string $that): bool
    {
        return $this->getMoney()->isLessThanOrEqualTo($this->getAmountOf($that));
    }

    /**
     * Checks if this number is strictly greater than the given one.
     */
    public function isGreaterThan(Money|BigNumber|int|float|string $that): bool
    {
        return $this->getMoney()->isGreaterThan($this->getAmountOf($that));
    }

    /**
     * Checks if this number is greater than or equal to the given one.
     */
    public function isGreaterThanOrEqualTo(Money|BigNumber|int|float|string $that): bool
    {
        return $this->getMoney()->isGreaterThanOrEqualTo($this->getAmountOf($that));
    }

    /**
     * Returns the sum of this Money and the given amount.
     *
     * If the operand is a Money, it must have the same context as this Money, or an exception is thrown. This is by design, to ensure that contexts are not mixed accidentally.
     *
     * The resulting Money has the same context as this Money. If the result needs rounding to fit this context, a rounding mode can be provided. If a rounding mode is not provided and rounding is necessary, an exception is thrown.
     */
    public function plus(Money|BigNumber|float|int|string $that, RoundingMode $roundingMode = RoundingMode::Unnecessary): static
    {
        return static::ofMoney($this->getMoney()->plus($this->getAmountOf($that), $roundingMode));
    }

    /**
     * Returns the difference of this Money and the given amount.
     *
     * If the operand is a Money, it must have the same context as this Money, or an exception is thrown. This is by design, to ensure that contexts are not mixed accidentally.
     *
     * The resulting Money has the same context as this Money. If the result needs rounding to fit this context, a rounding mode can be provided. If a rounding mode is not provided and rounding is necessary, an exception is thrown.
     */
    public function minus(Money|BigNumber|float|int|string $that, RoundingMode $roundingMode = RoundingMode::Unnecessary): static
    {
        return static::ofMoney($this->getMoney()->minus($this->getAmountOf($that), $roundingMode));
    }

    /**
     * Returns the product of this Money and the given number.
     *
     * The resulting Money has the same context as this Money. If the result needs rounding to fit this context, a rounding mode can be provided. If a rounding mode is not provided and rounding is necessary, an exception is thrown.
     */
    public function multipliedBy(BigNumber|float|int|string $that, RoundingMode $roundingMode = RoundingMode::Unnecessary): static
    {
        return static::ofMoney($this->getMoney()->multipliedBy($that, $roundingMode));
    }

    /**
     * Returns the result of the division of this Money by the given number.
     *
     * The resulting Money has the same context as this Money. If the result needs rounding to fit this context, a rounding mode can be provided. If a rounding mode is not provided and rounding is necessary, an exception is thrown.
     */
    public function dividedBy(BigNumber|float|int|string $that, RoundingMode $roundingMode = RoundingMode::Unnecessary): static
    {
        return static::ofMoney($this->getMoney()->dividedBy($that, $roundingMode));
    }

    /**
     * Returns the quotient of the division of this Money by the given number.
     *
     * The given number must be a integer value. The resulting Money has the same context as this Money. This method can serve as a basis for a money allocation algorithm.
     */
    public function quotient(BigNumber|int|float|string $that): Money
    {
        return static::ofMoney($this->getMoney()->quotient($that));
    }

    /**
     * Returns the quotient and the remainder of the division of this Money by the given number.
     *
     * The given number must be an integer value. The resulting monies have the same context as this Money. This method can serve as a basis for a money allocation algorithm.
     *
     * @return array{Money,Money}
     */
    public function quotientAndRemainder(BigNumber|int|float|string $that): array
    {
        [$quotient, $remainder] = $this->getMoney()->quotientAndRemainder($that);

        return [
            static::ofMoney($quotient),
            static::ofMoney($remainder),
        ];
    }

    /**
     * Allocates this Money according to a list of ratios.
     *
     * If the allocation yields a remainder, its amount is split over the first monies in the list, so that the total of the resulting monies is always equal to this Money.
     *
     * For example, given a `USD 49.99` money in the default context, `allocate(1, 2, 3, 4)` returns [`USD 5.00`, `USD 10.00`, `USD 15.00`, `USD 19.99`]
     *
     * The resulting monies have the same context as this Money.
     *
     * @return array<Money>
     */
    public function allocate(int ...$ratios): array
    {
        $monies = $this->getMoney()->allocate(...$ratios);

        return array_map(fn ($money) => static::ofMoney($money), $monies);
    }

    /**
     * Allocates this Money according to a list of ratios.
     *
     * The remainder is also present, appended at the end of the list.
     *
     * For example, given a `USD 49.99` money in the default context,
     *  `allocateWithRemainder(1, 2, 3, 4)` returns [`USD 4.99`, `USD 9.98`, `USD 14.97`, `USD 19.96`, `USD 0.09`]
     *
     * The resulting monies have the same context as this Money.
     *
     * @return array<Money>
     */
    public function allocateWithRemainder(int ...$ratios): array
    {
        $monies = $this->getMoney()->allocateWithRemainder(...$ratios);

        return array_map(fn ($money) => static::ofMoney($money), $monies);
    }

    /**
     * Splits this Money into a number of parts.
     *
     * If the division of this Money by the number of parts yields a remainder, its amount is split over the first
     * monies in the list, so that the total of the resulting monies is always equal to this Money.
     *
     * For example, given a `USD 100.00` money in the default context,
     * `split(3)` returns [`USD 33.34`, `USD 33.33`, `USD 33.33`]
     *
     * The resulting monies have the same context as this Money.
     *
     * @return array<Money>
     */
    public function split(int $parts): array
    {
        $monies = $this->getMoney()->split($parts);

        return array_map(fn ($money) => static::ofMoney($money), $monies);
    }

    /**
     * Splits this Money into a number of parts and a remainder.
     *
     * For example, given a `USD 100.00` money in the default context,
     *  `splitWithRemainder(3)` returns [`USD 33.33`, `USD 33.33`, `USD 33.33`, `USD 0.01`]
     *
     * The resulting monies have the same context as this Money.
     *
     * @return array<Money>
     */
    public function splitWithRemainder(int $parts): array
    {
        $monies = $this->getMoney()->splitWithRemainder($parts);

        return array_map(fn ($money) => static::ofMoney($money), $monies);
    }

    /**
     * Returns a Money whose value is the absolute value of this Money.
     *
     * The resulting Money has the same context as this Money.
     */
    public function abs(): Money
    {
        return static::ofMoney($this->getMoney()->abs());
    }

    /**
     * Returns a Money whose value is the negated value of this Money.
     *
     * The resulting Money has the same context as this Money.
     */
    public function negated(): Money
    {
        return static::ofMoney($this->getMoney()->negated());
    }

    /**
     * Converts this Money to another currency, using an exchange rate.
     *
     * By default, the resulting Money has the same context as this Money. This can be overridden by providing a Context.
     *
     * For example, converting a default money of `USD 1.23` to EUR with an exchange rate of `0.91` and RoundingMode::UP will yield `EUR 1.12`.
     */
    public function convertedTo(
        Currency|string $currency,
        BigNumber|float|int|string $exchangeRate,
        ?Context $context = null,
        RoundingMode $roundingMode = RoundingMode::Unnecessary
    ): Money {
        if (! $currency instanceof Currency) {
            $currency = Currency::of($currency);
        }

        return static::ofMoney($this->getMoney()->convertedTo(
            $currency->getCurrency(),
            $exchangeRate,
            $context,
            $roundingMode,
        ));
    }

    /**
     * Formats this Money.
     *
     * Note that this method uses `number_format`, which internally represents values using floating point arithmetic, so discrepancies can appear when formatting very large monetary values.
     */
    public function format(bool $allowWholeNumber = false): string
    {
        if ($allowWholeNumber && ! $this->getAmount()->hasNonZeroFractionalPart()) {
            $scale = 0;
        } else {
            $scale = $this->getAmount()->getScale();
        }

        $negative = $this->isNegative();
        $value = $this->getAmount()->toFloat();
        $amount = $negative ? -$value : $value;
        $thousands = $this->getCurrency()->getThousandSeparator();
        $decimals = $this->getCurrency()->getDecimalSeparator();
        $prefix = $this->getCurrency()->isSymbolFirst()
            ? $this->getCurrency()->getSymbol().($this->getCurrency()->isSymbolSpaced() ? ' ' : '')
            : '';
        $suffix = $this->getCurrency()->isSymbolFirst()
            ? ''
            : ($this->getCurrency()->isSymbolSpaced() ? ' ' : '').$this->getCurrency()->getSymbol();
        $value = number_format($amount, $scale, $decimals, $thousands);

        return ($negative ? '-' : '').$prefix.$value.$suffix;
    }

    /**
     * Formats this Money with the given locale.
     *
     * Note that this method uses `NumberFormatter`, which internally represents values using floating point arithmetic, so discrepancies can appear when formatting very large monetary values.
     */
    public function formatLocale(?string $locale = null, bool $allowWholeNumber = false, ?Closure $callback = null): string
    {
        $locale ??= static::locale();

        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);

        if ($allowWholeNumber && ! $this->getAmount()->hasNonZeroFractionalPart()) {
            $scale = 0;
        } else {
            $scale = $this->getAmount()->getScale();
        }

        $formatter->setSymbol(NumberFormatter::CURRENCY_SYMBOL, $this->getCurrency()->getSymbol());
        $formatter->setSymbol(NumberFormatter::DECIMAL_SEPARATOR_SYMBOL, $this->getCurrency()->getDecimalSeparator());
        $formatter->setSymbol(NumberFormatter::GROUPING_SEPARATOR_SYMBOL, $this->getCurrency()->getThousandSeparator());
        $formatter->setAttribute(NumberFormatter::GROUPING_SIZE, $this->getCurrency()->getThousandPlaces());
        $formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, $scale);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, $scale);

        if (is_callable($callback)) {
            $callback($formatter);
        }

        return (string) $formatter->formatCurrency($this->getAmount()->toFloat(), $this->getCurrency()->getCode());
    }

    /**
     * Returns the amount of the given parameter.
     *
     * If the parameter is money, its currency is checked against this money's currency.
     */
    protected function getAmountOf(Money|BigNumber|int|float|string $that): BigNumber|int|float|string
    {
        if ($that instanceof Money) {
            if (! $that->getCurrency()->is($this->getCurrency())) {
                throw MoneyMismatchException::currencyMismatch(
                    $this->getCurrency()->getCurrency(),
                    $that->getCurrency()->getCurrency(),
                );
            }

            return $that->getAmount();
        }

        return $that;
    }

    /**
     * Get the instance as an array.
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
        return $this->format(true);
    }
}
