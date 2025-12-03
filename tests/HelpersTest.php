<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Money;

it('returns a money object', function () {
    $this->assertEquals(Money::ofMinor(1000, Currency::of('USD')), money(1000));
});

it('returns a money object in major unit', function () {
    $this->assertEquals(Money::ofMinor(100000, Currency::of('USD')), money(1000, major: true));
});

it('returns a money object in different currencies', function () {
    $this->assertEquals(Money::ofMinor(100000, Currency::of('EUR')), money(1000, currency: 'EUR', major: true));
});

it('returns a currency object', function () {
    $this->assertEquals(Currency::of('USD'), currency('USD'));
});

it('returns a currency object in different currencies', function () {
    $this->assertEquals(Currency::of('EUR'), currency('EUR'));
});
