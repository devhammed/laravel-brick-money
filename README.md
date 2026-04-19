# Laravel Brick Money

[![Latest Version on Packagist](https://img.shields.io/packagist/v/devhammed/laravel-brick-money.svg?style=flat-square)](https://packagist.org/packages/devhammed/laravel-brick-money)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/devhammed/laravel-brick-money/run-tests.yml?branch=develop&label=tests&style=flat-square)](https://github.com/devhammed/laravel-brick-money/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/devhammed/laravel-brick-money.svg?style=flat-square)](https://packagist.org/packages/devhammed/laravel-brick-money)

## Table of Contents

- [Introduction](#introduction)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
    - [Available Methods](#available-methods)
    - [Eloquent](#eloquent)
        - [AsIntegerMoney Cast](#asintegermoney-recommended)
        - [AsDecimalMoney Cast](#asdecimalmoney)
        - [AsCurrency Cast](#ascurrency)
    - [HTTP](#http)
        - [Request Macros](#request-macros)
        - [Validation Rules](#validation-rules)
        - [JSON Serialization](#json-serialization)
    - [Helpers](#helpers)
    - [Views](#views)
        - [Components](#components)
        - [Directives](#directives)
    - [Extensions](#extensions)
        - [Macros](#macros)
        - [Mixins](#mixins)
    - [Livewire](#livewire)
    - [Filament](#filament)
        - [Plugin](#plugin)
        - [Forms](#forms)
        - [Tables](#tables)
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

// Creation
$usd = Currency::of('USD');
$eur = Currency::of('EUR');
$total = Money::of(1000, $usd);
$tax = Money::of(100, $usd);

// Money static methods
Money::of(100, $usd); // Get the Money instance of a numeric amount and currency.
Money::ofMinor(100, $usd); // Get the Money instance of a numeric amount in minor units and currency.
Money::ofMoney(\Brick\Money\Money::of(100, 'USD')); // Get the Money instance of a Brick\Money\Money instance.
Money::zero($usd); // Get the Money instance of zero amount and currency.
Money::min(Money::of(100, $usd), Money::of(200, $usd)); // Get the minimum of two or more Money instances.
Money::max(Money::of(100, $usd), Money::of(200, $usd)); // Get the maximum of two or more Money instances.
Money::total(Money::of(100, $usd), Money::of(200, $usd)); // Get the total of two or more Money instances.
Money::locale('en_US'); // Get or set the default locale for formatting.
Money::jsonSerializeMinorUnits(true); // Get or set whether to return minor units in JSON serialization.
Money::jsonSerializer(fn(Money $money) => [
    'amount' => (string) (Money::jsonSerializeMinorUnits() ?
    $money->getMinorAmount() : $money->getAmount(),
    'currency' => (string) $money->getCurrency(),
 ); // Get or set the JSON serializer for Money instances.

// Money instance methods
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
$total->convertedTo($eur, 0.91); // $910
$total->convertedTo('NGN', 1457); // N1,457,000
$total->negated(); // -$1,000
$total->format(); // "$1,000"

# Currency static methods
Currency::of('USD'); // Get the Currency instance of a currency code.
Currency::of(\Brick\Money\Currency::of('USD')); // Get the Currency instance of a Brick\Money\Currency instance.
Currency::ofCountry('NG'); // Get the Currency instance for a country code.
Currency::currencies(); // Get an array of all configured currencies.
Currency::jsonSerializer(fn(Currency $currency) => [
    'name' => $currency->getName(),
    'code' => $currency->getCode(),
]); // Get or set the JSON serializer for Currency instances.

# Currency instance methods
$usd->getCode(); // "USD"
$usd->getName(); // "US Dollar"
$usd->getNumericCode(); // 840
$usd->getSymbol(); // $
$usd->isSymbolFirst(); // true
$usd->isSymbolSpaced(); // false
$usd->getDecimalPlaces(); // 2
$usd->getDecimalSeparator(); // "."
$usd->getThousandPlaces(); // 3
$usd->getThousandSeparator(); // ","
$usd->getCurrency(); // Brick\Money\Currency{}
$usd->is($eur); // false
```

### Eloquent

This package provides Model casts to help with money and currency storage in the database.

There are currently two supported storage cast types, and each of them support either storing in a single JSON column or
separate amount and currency columns:

#### `AsIntegerMoney` (Recommended)

This stores the amount in the currency's minor unit.

Example Model:

```php
use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Casts\AsIntegerMoney;

/**
 * @property Money $amount
 * @property string $currency
 * @property Money $tax
 */
class Transaction extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => AsIntegerMoney::of('currency'),
            'tax' => AsIntegerMoney::class,
        ];
    }
}
```

Example Migration:

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::create('transactions', function (Blueprint $table) {
    $table->id();

    $table->bigInteger('amount');   // e.g., 1000 = $10.00,
    $table->string('currency');  // currency code e.g. USD
    $table->json('tax'); // {"amount": "2345", "currency": "TRX"}

    $table->timestamps();
});
```

> Some cryptocurrencies, particularly those with very high precision (e.g., ETH uses 18 decimal places) will exceed the
> limit of `->bigInteger()` so it is recommended to use `->string()` in this case when you
> are using the separate columns mode.

#### `AsDecimalMoney`

This stores the amount in the currency's major unit.

Example Model:

```php
use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Casts\AsDecimalMoney;

/**
 * @property Money $amount
 * @property string $currency
 * @property Money $tax
 */
class Transaction extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => AsDecimalMoney::of('currency'),
            'tax' => AsDecimalMoney::class,
        ];
    }
}
```

Example Migration:

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::create('transactions', function (Blueprint $table) {
    $table->id();

    $table->decimal('amount', 36, 18);  // e.g., 10.00 = $10.00,
    $table->string('currency');  // currency code e.g. USD
    $table->json('tax'); // {"amount": "23.45", "currency": "TRX"}

    $table->timestamps();
});
```

> The reason for using `DECIMAL(36, 18)` in the separate column example is to accommodate cryptocurrencies with very
> high precision like ETH that uses 18 decimal places, you can reduce or increase the scale and precision to match your
> project requirements.

#### `AsCurrency`

This is useful for casting currency columns to `Currency`.

Example Model:

```php
use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Casts\AsCurrency;

/**
 * @property Currency $currency
 */
class Transaction extends Model
{
    protected function casts(): array
    {
        return [
            'currency' => AsCurrency::class,
        ];
    }
}
```

Example Migration:

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->string('currency');  // currency code e.g. USD
    $table->timestamps();
});
```

> This can also be used in conjunction with the currency column of the amount casts explained above.

### HTTP

This package provides request macros and validation rules to work with `money`/`currency` in HTTP Requests.

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

#### JSON Serialization

This package provides JSON serialization for both `Money` and `Currency` objects but you can customize
according to your project requirements.

##### Money

Default:

```json
{
    "amount": "1.00",
    "currency": {
        "name": "US Dollar",
        "code": "USD",
        "numeric_code": 840,
        "symbol": "$",
        "symbol_first": true,
        "symbol_spaced": false,
        "decimal_places": 2,
        "decimal_separator": ".",
        "thousand_places": 3,
        "thousand_separator": ","
    }
}
```

The `amount` field respects the JSON serialization unit of the `Money` object (which defaults to `brick-money.minor` configuration). If set to `true`, the amount is returned in minor units (e.g., cents for USD). Otherwise, it is returned in major units (e.g., dollars for USD):

```php
use Devhammed\LaravelBrickMoney\Money;

Money::jsonSerializeMinorUnits(true); // or `false` to return major units.
```

You can also customize the whole JSON serialization process by providing a custom serializer:

```php
use Devhammed\LaravelBrickMoney\Money;

Money::jsonSerializer(fn(Money $money) => [
    'amount' => (string) (Money::jsonSerializeMinorUnits() ? $money->getMinorAmount() : $money->getAmount()),
    'currency' => (string) $money->getCurrency(),
]);
```

Which will return something like this:

```json
{
    "amount": "1.00",
    "currency": "USD"
}
```

##### Currency

Default:

```json
{
    "name": "US Dollar",
    "code": "USD",
    "numeric_code": 840,
    "symbol": "$",
    "symbol_first": true,
    "symbol_spaced": false,
    "decimal_places": 2,
    "decimal_separator": ".",
    "thousand_places": 3,
    "thousand_separator": ","
}
```

Customize:

```php
use Devhammed\LaravelBrickMoney\Currency;

Currency::jsonSerializer(fn(Currency $currency) => [
    'name' => $currency->getName(),
    'code' => $currency->getCode(),
]);
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

Money::zero('USD')->plus(100)->withPercentage(10); // $110
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

Money::zero('USD')->plus(100)->withPercentage(10); // $110
```

## Livewire

This package provides automatic support for [Livewire](https://livewire.laravel.com/) by registering property synthesizers for `Money` and `Currency` objects.

This allows you to use `Money` and `Currency` objects directly as Livewire properties:

```php
use Devhammed\LaravelBrickMoney\Money;
use Livewire\Component;

class Wallet extends Component
{
    public Money $balance;

    public function mount()
    {
        $this->balance = Money::of(100, 'USD');
    }

    public function addTenDollars()
    {
        $this->balance = $this->balance->plus(10);
    }

    public function render()
    {
        return view('livewire.wallet');
    }
}
```

## Filament

This package also provides a [Filament](https://filamentphp.com/) plugin and components to work with money and currency.

### Forms

The `MoneyInput` component allows you to manage money fields in your Filament forms. It provides a fused group of an amount input and a currency selector.

```php
use Devhammed\LaravelBrickMoney\Filament\Forms\Components\MoneyInput;

MoneyInput::for('price')
```

It will automatically use the configured currencies and default currency to populate the currency selector.

> You must not use the `make()` method on the `MoneyInput` class directly, as we extended the `FusedGroup` component to provide this functionality and PHP does not support overriding methods with different signatures.

You can also specify a custom list of currencies and a default currency:

```php
use Devhammed\LaravelBrickMoney\Filament\Forms\Components\MoneyInput;

MoneyInput::for('price')
    ->defaultCurrency('USD')
    ->currencies(['USD' => 'US Dollar', 'EUR' => 'Euro'])
```

You can also fix the currency so only the amount is editable:

```php
MoneyInput::for('price')
    ->fixedCurrency()
```

And you can customize the underlying input and select components:

```php
MoneyInput::for('price')
    ->currencySelect(fn ($select) => $select->columnSpan(['xl' => 3]))
    ->amountInput(fn ($input) => $input->columnSpan(['xl' => 9]))
```

### Tables

The `MoneyColumn` allows you to display money values in your Filament tables.

```php
use Devhammed\LaravelBrickMoney\Filament\Tables\Columns\MoneyColumn;

MoneyColumn::make('price')
```

It will automatically format the `Money` object using its `format()` method, and it extended the `TextColumn` component so you can use any of its methods to further customize the column.

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
