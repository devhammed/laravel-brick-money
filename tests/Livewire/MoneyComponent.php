<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests\Livewire;

use Devhammed\LaravelBrickMoney\Money;
use Livewire\Component;

class MoneyComponent extends Component
{
    public Money $price;

    public function mount(int $amount, string $currency): void
    {
        $this->price = Money::of($amount, $currency);
    }

    public function render(): string
    {
        return <<<'BLADE'
        <div>
            <input type="number" wire:model.live="price.amount">
            <select wire:model.live="price.currency">
                <option value="USD">US Dollar</option>
                <option value="EUR">Euro</option>
            </select>
            <p>Amount: {{ $price->getAmount() }}</p>
            <p>Currency: {{ $price->getCurrency()->getName() }}</p>
        </div>
        BLADE;
    }
}
