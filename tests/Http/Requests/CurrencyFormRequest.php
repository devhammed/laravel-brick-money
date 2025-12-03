<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests\Http\Requests;

use Devhammed\LaravelBrickMoney\Rules\CurrencyRule;
use Illuminate\Foundation\Http\FormRequest;

class CurrencyFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'currency' => [
                new CurrencyRule,
            ],
        ];
    }
}
