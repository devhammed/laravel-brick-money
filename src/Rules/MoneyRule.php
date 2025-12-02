<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Rules;

use Closure;
use Brick\Math\BigNumber;
use Devhammed\LaravelBrickMoney\Money;
use Illuminate\Contracts\Validation\ValidationRule;

class MoneyRule implements ValidationRule
{
    public function __construct(
        public bool $nullable = true,
        public Money|BigNumber|int|float|string|null $min = null,
        public Money|BigNumber|int|float|string|null $max = null,
    ) {
        //
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
    }
}
