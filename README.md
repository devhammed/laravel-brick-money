# Laravel Brick Money

[![Latest Version on Packagist](https://img.shields.io/packagist/v/devhammed/laravel-brick-money.svg?style=flat-square)](https://packagist.org/packages/devhammed/laravel-brick-money)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/devhammed/laravel-brick-money/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/devhammed/laravel-brick-money/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/devhammed/laravel-brick-money.svg?style=flat-square)](https://packagist.org/packages/devhammed/laravel-brick-money)

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
    - [Available Methods](#available-methods)
    - [Eloquent](#eloquent)
    - [HTTP](#http)
        - [Request Macros](#request-macros)
        - [Validation Rules](#validation-rules)
    - [Helpers](#helpers)
    - [Views](#views)
        - [Components](#components)
        - [Directives](#directives)
    - [Extensions](#extensions)
        - [Macros](#macros)
        - [Mixins](#mixins)
- [Testing](#testing)
- [Changelog](#changelog)
- [Security](#security)
- [Credits](#credits)
- [License](#license)

## Introduction

This package provides the Laravel integration of [Brick/Money](https://github.com/brick/money).

## Installation

Install via Composer:

```bash
composer require devhammed/laravel-brick-money
```

## Configuration

Publish the configuration file if you need to customize defaults:

```bash
php artisan vendor:publish --tag="brick-money-config"
```

You can view the contents of the default configuration in [config/brick-money.php](./config/brick-money.php)

## Usage

```php
use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Currency;

echo Money::of(100); // '$100.00'

echo Money::of(100, Currency::of('EUR')); // '€100,00'

echo Money::of(100, 'USD'); // '$100.00'

echo Money::ofMinor(100); // '$1.00'

echo Money::ofMinor(100, 'EUR'); // '€1,00'
```

### Available Methods

The package has tried to wrap as many `brick/money` package methods as possible
but if you encounter a method that we haven't wrapped yet, you can get the underlying instance by calling `->getMoney()`
on `Money` or `->getCurrency()` on `Currency` and
call it directly:

```php
use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Currency;

$usd = Currency::of('USD');
$eur = Currency::of('EUR');
$total = Money::of(1000, $usd);
$tax = Money::of(100, $usd);

// Money methods
$total->getCurrency(); // Devhammed\LaravelBrickMoney\Currency{}
$total->getMoney(); // Brick\Money\Money{}
$total->getContext(); // Brick\Money\Context\DefaultContext{}
$total->getAmount(); // Brick\Math\BigDecimal{}
$total->getMinorAmount(); // Brick\Math\BigDecimal{}
$total->getUnscaledAmount(); // Brick\Math\BigInteger{}
$total->getSign(); // 1
$total->isZero(); // false
$total->isNegative(); // false
$total->isNegativeOrZero(); // false
$total->isPositive(); // true
$total->isPositiveOrZero(); // true
$total->compareTo($tax); // 1
$total->isEqualTo($tax); // false
$total->isLessThan($tax); // false
$total->isLessThanOrEqualTo($tax); // false
$total->isGreaterThan($tax); // true
$total->isGreaterThanOrEqualTo($tax); // true
$total->plus($tax); // $1,100
$total->minus($tax); // $900
$total->multipliedBy(2); // $2,000
$total->dividedBy(2); // $500
$total->allocate(1, 2, 3);
$total->split(2); // [$500, $500]
$total->splitWithRemainder(3); // [$33.33, $33.33, $33.33, $0.01]
$total->abs(); // $1,000
$total->negated(); // -$1,000
$total->format(); // "$1,000"

# Currency methods
$usd->getCode(); // "USD"
$usd->getName(); // "US Dollar"
$usd->getNumericCode(); // 840
$usd->getSymbol(); // $
$usd->isSymbolFirst(); // true
$usd->isSymbolSpaced(); // false
$usd->getDecimalPlaces(); // 2
$usd->getDecimalSeparator(); // "."
$usd->getThousandSeparator(); // ","
$usd->getPrefix(); // "$"
$usd->getSuffix(); // ""
$usd->getCurrency(); // Brick\Money\Currency{}
$usd->is($eur); // false
```

### Eloquent

This package provides Model casts to help with money and currency storage in the database.

### HTTP

This package provides helpers to work with `money`/`currency` in HTTP Requests.

#### Request Macros

```php
request()->currency('currency'); // Devhammed\LaravelBrickMoney\Currency{}

request()->money('price'); // Devhammed\LaravelBrickMoney\Money{}

request()->money('price', currency: 'EUR', minor: true); // Devhammed\LaravelBrickMoney\Money{}
```

#### Validation Rules

```php
namespace App\Http\Requests;

use Devhammed\LaravelBrickMoney\Rules\MoneyRule;
use Devhammed\LaravelBrickMoney\Rules\CurrencyRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'price' => [
                'required',
                new MoneyRule(
                    min: 0,
                    max: 1000,
                ),
            ],
            'currency' => [
                'required',
                new CurrencyRule(),
            ],
        ];
    }
}
```

### Helpers

```php
money(100) // $100.00

money(100, minor: true) // $1.00

money(100, 'EUR') // €100,00

currency('USD') // USD
```

### Views

This package provides helpers to work with `money` and `currency` in Blade templates.

#### Components

```html

<x-money amount="100"/> <!-- $100.00 -->

<x-money amount="100" currency="USD"/> <!-- $100.00 -->

<x-money amount="100" currency="USD" minor/> <!-- $1.00 -->

<x-currency currency="USD"/> <!-- USD -->
```

#### Directives

```php
@money(100) // $100.00

@money(100, minor: true) // $1.00

@money(100, 'EUR') // €100,00

@currency('USD') // USD
```

### Extensions

The `Money` and `Currency` classes implements the Laravel `Macroable` trait, allowing you to add custom functionalities
through macros and mixins.

#### Macros

```php
use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Currency;

Money::macro('zero', fn(Currency|string $currency) => Money::of(0, $currency));

Money::macro('withPercentage', function (float $percentage): Money {
    return $this->multipliedBy(1 + ($percentage / 100));
});

Money::zero()->plus(100)->withPercentage(10); // $110
```

#### Mixins

And with mixins, you can achieve the same thing using a dedicated class:

```php
use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Currency;

class MoneyExtensions
{
    public static function zero(Currency|string $currency): Money
    {
        return Money::of(0, $currency);
    }
    
    public function withPercentage(float $percentage): Money
    {
        return $this->multipliedBy(1 + ($percentage / 100));
    }
}

Money::mixin(new MoneyExtensions());

Money::zero()->plus(100)->withPercentage(10); // $110
```

## Testing

Run the test suite:

```bash
composer test
```

## Changelog

See the [CHANGELOG](CHANGELOG.md) for a full history of updates.

## Security

If you discover a security vulnerability, please refer to the [security policy](../../security/policy).

## Credits

- [Hammed Oyedele](https://github.com/devhammed)
- [All Contributors](../../contributors)

## License

This package is open-source software released under the MIT License.

See [LICENSE.md](LICENSE.md) for details.
