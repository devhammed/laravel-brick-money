# Laravel Brick Money

[![Latest Version on Packagist](https://img.shields.io/packagist/v/devhammed/laravel-brick-money.svg?style=flat-square)](https://packagist.org/packages/devhammed/laravel-brick-money)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/devhammed/laravel-brick-money/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/devhammed/laravel-brick-money/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/devhammed/laravel-brick-money.svg?style=flat-square)](https://packagist.org/packages/devhammed/laravel-brick-money)

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
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

echo Money::of(100, Currency::of('EUR')) // '€100,00'

echo Money::of(100, 'USD') // '$100.00'

echo Money::ofMinor(100); // '$1.00'

echo Money::ofMinor(100, 'EUR'); // '€1,00'
```

### Helpers

```php
money(100) // $1.00

money(100, major: true) // $100

money(100, 'EUR') // €1,00

currency('USD') // USD
```

### Blade Components

```html

<x-money amount="100"/> <!-- $1.00 -->

<x-money amount="100" currency="USD"/> <!-- $1.00 -->

<x-money amount="100" currency="USD" major/> <!-- $100.00 -->

<x-currency currency="USD"/> <!-- USD -->
```

### Blade Directives

```php
@money(100) // $1.00
@money(100, major: true) // $100
@money(100, 'EUR') // €1,00
@currency('USD') // USD
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
