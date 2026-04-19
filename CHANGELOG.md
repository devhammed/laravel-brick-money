# Changelog

All notable changes to `laravel-brick-money` will be documented in this file.

## v1.0.7

### What's Changed

- Added `Money::min(...$monies)` method
- Added `Money::max(...$monies)` method
- Added `Money::total(...$monies)` method
- Added `Money::avg(...$monies)` method
- Added `Currency::ofCountry($countryCode)` method
- Added `Currency::currencies()` method
- Implemented the `MoneyContainer` interface from brick/money package

Full Changelog: https://github.com/devhammed/laravel-brick-money/compare/1.0.6...1.0.7

## v1.0.6

### What's Changed

- The JSON serializer unit is now configurable.
- BREAKING CHANGE: The default JSON serializer now respects the `brick-money.minor` configuration so you might start seeing major units in your JSON responses.

Full Changelog: https://github.com/devhammed/laravel-brick-money/compare/1.0.5...1.0.6

## v1.0.5

### What's Changed

- Fixed issue with Composer package tagging

Full Changelog: https://github.com/devhammed/laravel-brick-money/compare/1.0.4...1.0.5

## v1.0.4

### What's Changed

- Dropped Laravel 10 support
- Dropped PHP 8.2 support
- Added `MoneyInput` component for Filament Forms
- Added `MoneyField` component for Filament Tables

Full Changelog: https://github.com/devhammed/laravel-brick-money/compare/1.0.3...1.0.4

## v1.0.3

### What's Changed

- Laravel 13 compatibility

Full Changelog: https://github.com/devhammed/laravel-brick-money/compare/1.0.2...1.0.3

## v1.0.2

### What's Changed

- Renamed casts to match Laravel best practices (BREAKING CHANGE).
- Moved `AsMoneyCast` abstract class logic to a trait to avoid mistakenly using it.

**Full Changelog**: https://github.com/devhammed/laravel-brick-money/compare/1.0.1...1.0.2

## v1.0.1

### What's Changed

- Fixed issue with currency symbol spacing
- Improved money and currency JSON serialization
- Add support for currency thousand places

**Full Changelog**: https://github.com/devhammed/laravel-brick-money/compare/1.0.0...1.0.1

## v1.0.0

### What's Changed

- First release

**Full Changelog**: https://github.com/devhammed/laravel-brick-money/commits/1.0.0
