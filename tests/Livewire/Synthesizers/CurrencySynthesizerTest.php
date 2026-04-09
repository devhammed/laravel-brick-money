<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Livewire\Synthesizers\CurrencySynthesizer;
use Devhammed\LaravelBrickMoney\Tests\Livewire\Components\CurrencyComponent;
use Livewire\Livewire;

use function Pest\Livewire\livewire;

beforeEach(function () {
    Livewire::propertySynthesizer(CurrencySynthesizer::class);
});

it('can initialize a currency object', function () {
    livewire(CurrencyComponent::class, ['code' => 'EUR'])
        ->assertSee('Currency: Euro');
});

it('can set a currency object', function () {
    livewire(CurrencyComponent::class, ['code' => 'EUR'])
        ->assertSee('Currency: Euro')
        ->set('currency', 'USD')
        ->assertSee('Currency: US Dollar');
});
