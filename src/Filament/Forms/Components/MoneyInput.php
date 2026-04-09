<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Filament\Forms\Components;

use Closure;
use Devhammed\LaravelBrickMoney\Currency;
use Devhammed\LaravelBrickMoney\Filament\Forms\StateCasts\CurrencyStateCast;
use Devhammed\LaravelBrickMoney\Money;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\RawJs;

class MoneyInput extends FusedGroup
{
    protected Select $currencySelect;

    protected TextInput $amountInput;

    /** @var Closure(): (Currency|string|null)|Currency|string|null */
    protected Closure|Currency|string|null $defaultCurrency = null;

    protected Closure|bool $isFixedCurrency = false;

    /** @var array<string, string>|Closure(): (array<string, string>|null)|null */
    protected Closure|array|null $currencies = null;

    public static function make(array|Closure $schema = []): static
    {
        throw new Exception('Kindly use the MoneyInput::for() method instead.');
    }

    public static function for(string $name): static
    {
        $static = app(static::class);

        $static->currencySelect = Select::make('currency')
            ->selectablePlaceholder(false)
            ->columnSpan([
                'default' => 3,
                'xl' => 2,
            ])
            ->extraAttributes([
                'class' => 'money-input__currency',
            ])
            ->options(fn (): array => $static->getCurrencies())
            ->stateCast(fn (): CurrencyStateCast => app(CurrencyStateCast::class))
            ->afterStateUpdated(fn (Set $set) => $set('amount', '0'))
            ->dehydrateStateUsing(fn (string $state): string => $static->isFixedCurrency() ? $static->getDefaultCurrency() : $state)
            ->disabled(fn (): bool => $static->isFixedCurrency())
            ->saved()
            ->searchable()
            ->live()
            ->required();

        $static->amountInput = TextInput::make('amount')
            ->columnSpan([
                'default' => 9,
                'xl' => 10,
            ])
            ->mask(function (Get $get) use ($static): RawJs {
                $currencyCode = $get('currency');

                if (blank($currencyCode)) {
                    $currencyCode = $static->getDefaultCurrency();
                }

                $currency = currency($currencyCode);

                return RawJs::make(sprintf(
                    "\$money(\$input, '%s', '%s', %d);",
                    $currency->getDecimalSeparator(),
                    $currency->getThousandSeparator(),
                    $currency->getDecimalPlaces(),
                ));
            })
            ->dehydrateStateUsing(function (Get $get, ?string $state) use ($static): string {
                $currencyCode = $get('currency');

                if (blank($currencyCode)) {
                    $currencyCode = $static->getDefaultCurrency();
                }

                $currency = currency($currencyCode);

                return str($state ?? '0')
                    ->replace($currency->getThousandSeparator(), '')
                    ->replace($currency->getDecimalSeparator(), '.')
                    ->squish()
                    ->value();
            })
            ->extraAttributes(function (TextInput $component, Get $get) use ($static): array {
                $currencyCode = $get('currency');

                if (blank($currencyCode)) {
                    $currencyCode = $static->getDefaultCurrency();
                }

                return [
                    'class' => 'money-input__amount',
                    'wire:key' => "{$component->getKey()}-{$currencyCode}",
                ];
            })
            ->required();

        return $static
            ->configure()
            ->label($name)
            ->statePath($name)
            ->columns([
                'default' => 12,
            ])
            ->extraFieldWrapperAttributes([
                'class' => 'money-input',
            ])
            ->dehydrateStateUsing(fn (?array $state): Money => $static->normalizeMoney($state))
            ->formatStateUsing(function (Money|array|null $state) use ($static): array {
                $state = $static->normalizeMoney($state, true);

                return [
                    'amount' => str($state)->replace($state->getCurrency()->getSymbol(), '')->squish()->value(),
                    'currency' => (string) $state->getCurrency(),
                ];
            })
            ->schema(function (Money|array|null $state) use ($static): array {
                $state = $static->normalizeMoney($state);

                if (! $state->getCurrency()->isSymbolFirst()) {
                    return [
                        $static->amountInput,
                        $static->currencySelect,
                    ];
                }

                return [
                    $static->currencySelect,
                    $static->amountInput,
                ];
            });
    }

    /** @param  Closure(TextInput): void  $callback */
    public function amountInput(Closure $callback): static
    {
        $callback($this->amountInput);

        return $this;
    }

    /** @param  Closure(Select): void  $callback */
    public function currencySelect(Closure $callback): static
    {
        $callback($this->currencySelect);

        return $this;
    }

    /** @param  Closure(): (Currency|string|null)|Currency|string|null  $currency */
    public function defaultCurrency(Closure|Currency|string|null $currency): static
    {
        $this->defaultCurrency = $currency;

        return $this;
    }

    public function fixedCurrency(Closure|bool $condition = true): static
    {
        $this->isFixedCurrency = $condition;

        return $this;
    }

    /** @param array<string, string>|Closure(): (array<string, string>|null)|null  $values */
    public function currencies(Closure|array|null $values): static
    {
        $this->currencies = $values;

        return $this;
    }

    public function getDefaultCurrency(): string
    {
        $currency = $this->evaluate($this->defaultCurrency) ?? currency();

        if (is_string($currency)) {
            return $currency;
        }

        return $currency->getCode();
    }

    public function isFixedCurrency(): bool
    {
        return $this->evaluate($this->isFixedCurrency);
    }

    /** @return array<string, string> */
    public function getCurrencies(): array
    {
        $options = $this->evaluate($this->currencies);

        if (filled($options)) {
            return $options;
        }

        $currencies = Currency::currencies();

        $codes = array_keys($currencies);

        return array_combine($codes, $codes);
    }

    public function normalizeMoney(mixed $state, bool $isFormatting = false): Money
    {
        if (
            is_array($state)
            && isset($state['amount'], $state['currency'])
            && filled($state['amount'])
            && filled($state['currency'])
            && is_numeric($state['amount'])
            && (is_string($state['currency']) || (is_array($state['currency']) && isset($state['currency']['code'])))
        ) {
            /** @var numeric-string $amount */
            $amount = $state['amount'];

            /** @var string $currency */
            $currency = is_array($state['currency'])
                ? $state['currency']['code']
                : $state['currency'];

            return money($amount, $currency, $isFormatting && Money::jsonSerializeMinorUnits());
        }

        if ($state instanceof Money) {
            return $state;
        }

        return money(0, $this->getDefaultCurrency());
    }
}
