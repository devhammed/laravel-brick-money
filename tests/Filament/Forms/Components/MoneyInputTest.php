<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Filament\Forms\Components\MoneyInput;
use Devhammed\LaravelBrickMoney\Tests\Filament\Pages\CreateProduct;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

use function Pest\Livewire\livewire;

it('has a price field', function () {
    livewire(CreateProduct::class)
        ->assertSchemaComponentExists('price', checkComponentUsing: function (MoneyInput $component) {
            return $component->getDefaultCurrency() === 'USD';
        })
        ->assertFormFieldExists('price.amount', function (TextInput $field) {
            return $field->getState() === '0';
        })
        ->assertFormFieldExists('price.currency', function (Select $field) {
            return $field->getState() === 'USD' && $field->isEnabled();
        });
});

it('has a price field with fixed currency', function () {
    livewire(CreateProduct::class)
        ->assertSchemaComponentExists('fixed_currency_price', checkComponentUsing: function (MoneyInput $component) {
            return $component->getDefaultCurrency() === 'EUR';
        })
        ->assertFormFieldExists('fixed_currency_price.currency', function (Select $field) {
            return $field->isDisabled();
        });
});

it('has a default price', function () {
    livewire(CreateProduct::class, ['amount' => 100])
        ->assertSchemaComponentExists('default_price')
        ->assertFormFieldExists('default_price.amount', function (TextInput $field) {
            return $field->getState() === '100';
        })
        ->assertFormFieldExists('default_price.currency', function (Select $field) {
            return $field->getState() === 'NGN' && $field->isEnabled();
        });
});
