<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Filament;

use Devhammed\LaravelBrickMoney\Money;
use Filament\Contracts\Plugin;
use Filament\Panel;

class MoneyPlugin implements Plugin
{
    public const string ID = 'money';

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return static::ID;
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        Money::jsonSerializer(fn (Money $money) => [
            'amount' => (string) $money->getAmount(),
            'currency' => (string) $money->getCurrency(),
        ]);
    }
}
