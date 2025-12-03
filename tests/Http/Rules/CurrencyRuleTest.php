<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Tests\Http\Requests\CurrencyFormRequest;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::post('/test-currency-rule', function (CurrencyFormRequest $request) {
        return $request->currency('currency');
    });
});
