<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests\Http\Requests;

use Devhammed\LaravelBrickMoney\Rules\MoneyRule;
use Illuminate\Foundation\Http\FormRequest;

class MoneyFormRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'price' => [
                new MoneyRule(
                    min: 0,
                    max: 1000,
                ),
            ],
        ];
    }
}
