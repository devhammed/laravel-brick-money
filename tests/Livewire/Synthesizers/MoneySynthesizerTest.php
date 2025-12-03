<?php

declare(strict_types=1);

use Devhammed\LaravelBrickMoney\Livewire\Synthesizers\MoneySynthesizer;
use Devhammed\LaravelBrickMoney\Tests\Livewire\Components\MoneyComponent;
use Livewire\Livewire;
use Livewire\LivewireServiceProvider;

use function Pest\Livewire\livewire;

beforeEach(function () {
    app()->register(LivewireServiceProvider::class);

    Livewire::propertySynthesizer(MoneySynthesizer::class);
});

it('can initialize a money object', function () {
    livewire(MoneyComponent::class, ['amount' => 100, 'currency' => 'USD'])
        ->assertSee('Amount: 100');
});

it('can set a money object properties', function () {
    livewire(MoneyComponent::class, ['amount' => 100, 'currency' => 'USD'])
        ->set('price.amount', 200)
        ->set('price.currency', 'EUR')
        ->assertSee('Amount: 200')
        ->assertSee('Currency: Euro');
});
