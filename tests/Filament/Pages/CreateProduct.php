<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests\Filament\Pages;

use Devhammed\LaravelBrickMoney\Filament\Forms\Components\MoneyInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

/**
 * @property-read Schema $form
 */
class CreateProduct extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(int $amount = 0): void
    {
        $this->form->fill([
            'default_price' => money($amount, 'NGN'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                MoneyInput::for('price'),
                MoneyInput::for('default_price'),
                MoneyInput::for('fixed_currency_price')
                    ->defaultCurrency('EUR')
                    ->fixedCurrency(),
            ]);
    }

    public function save(): void
    {
        //
    }

    public function render(): string
    {
        return <<<'BLADE'
        <div>
            <form wire:submit="save">
                {{ $this->form }}

                <button type="submit">
                    Submit
                </button>
            </form>

            <x-filament-actions::modals />
        </div>
        BLADE;
    }
}
