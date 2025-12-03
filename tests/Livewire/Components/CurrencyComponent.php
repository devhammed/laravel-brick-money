<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests\Livewire\Components;

use Devhammed\LaravelBrickMoney\Currency;
use Livewire\Component;

class CurrencyComponent extends Component
{
    public Currency $currency;

    public function mount(string $code = 'USD'): void
    {
        $this->currency = Currency::of($code);
    }

    public function render(): string
    {
        return <<<'BLADE'
        <div>
            <select wire:model.live="currency">
                <option value="USD">US Dollar</option>
                <option value="EUR">Euro</option>
            </select>
            <p>Currency: {{ $currency->getName() }}</p>
        </div>
        BLADE;
    }
}
