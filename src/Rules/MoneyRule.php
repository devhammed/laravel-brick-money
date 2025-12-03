<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Rules;

use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Closure;
use Devhammed\LaravelBrickMoney\Money;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class MoneyRule implements ValidationRule
{
    public function __construct(
        public Money|BigNumber|int|float|string|null $min = null,
        public Money|BigNumber|int|float|string|null $max = null,
        public ?string $currency = null,
        public ?bool $minor = null,
        public ?Context $context = null,
        public RoundingMode $roundingMode = RoundingMode::UNNECESSARY
    ) {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
            $fail(__('brick-money::validation.invalid_money'));

            return;
        }

        $money = money($value, $this->currency, $this->minor, $this->context, $this->roundingMode);

        if ($this->min !== null && $money->isLessThan($this->min)) {
            $fail(__('brick-money::validation.min_money'));
        }

        if ($this->max !== null && $money->isGreaterThan($this->max)) {
            $fail(__('brick-money::validation.max_money'));
        }
    }
}
