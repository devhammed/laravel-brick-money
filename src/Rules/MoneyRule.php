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
use Throwable;

class MoneyRule implements ValidationRule
{
    public function __construct(
        public Money|BigNumber|int|float|string|null $min = null,
        public Money|BigNumber|int|float|string|null $max = null,
        public ?string $currency = null,
        public ?bool $minor = null,
        public ?Context $context = null,
        public RoundingMode $roundingMode = RoundingMode::Unnecessary
    ) {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  string|int|float  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $money = money($value, $this->currency, $this->minor, $this->context, $this->roundingMode);

            if ($this->min !== null && $money->isLessThan($this->min)) {
                $fail(__('brick-money::validation.min_money'))->translate([
                    'min' => $this->min,
                ]);
            }

            if ($this->max !== null && $money->isGreaterThan($this->max)) {
                $fail(__('brick-money::validation.max_money'))->translate([
                    'max' => $this->max,
                ]);
            }
        } catch (Throwable $e) {
            report($e);

            $fail(__('brick-money::validation.invalid_money'))->translate();
        }
    }
}
