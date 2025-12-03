<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Rules;

use Closure;
use Devhammed\LaravelBrickMoney\Currency;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CurrencyRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! (is_string($value) && array_key_exists(mb_strtoupper($value), Currency::getCurrencies()))) {
            $fail(__('brick-money::validation.invalid_currency'))->translate();
        }
    }
}
