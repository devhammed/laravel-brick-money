<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Filament\Forms\StateCasts;

use BackedEnum;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;

class CurrencyStateCast implements StateCast
{
    public function __construct(
        protected bool $isNullable = true,
    ) {}

    public function get(mixed $state): ?string
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        if (is_array($state) && isset($state['code'])) {
            return (string) $state['code'];
        }

        if ($state instanceof BackedEnum) {
            $state = $state->value;
        }

        return (string) $state;
    }

    public function set(mixed $state): ?string
    {
        if ($this->isNullable && blank($state)) {
            return null;
        }

        if (is_array($state) && isset($state['code'])) {
            return (string) $state['code'];
        }

        if ($state instanceof BackedEnum) {
            $state = $state->value;
        }

        return (string) $state;
    }
}
