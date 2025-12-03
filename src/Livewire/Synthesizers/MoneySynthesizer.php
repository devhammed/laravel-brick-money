<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Livewire\Synthesizers;

use Brick\Money\Context\AutoContext;
use Brick\Money\Context\CashContext;
use Brick\Money\Context\CustomContext;
use Brick\Money\Context\DefaultContext;
use Brick\Money\Exception\MoneyMismatchException;
use Devhammed\LaravelBrickMoney\Money;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class MoneySynthesizer extends Synth
{
    public static string $key = 'money';

    public static function match(mixed $target): bool
    {
        return $target instanceof Money;
    }

    /** @return array{array<string,mixed>, array<mixed>} */
    public function dehydrate(Money $target): array
    {
        $context = $target->getContext();

        if ($context instanceof CustomContext) {
            $context = ['custom', [$context->getScale(), $context->getScale()]];
        } elseif ($context instanceof AutoContext) {
            $context = ['auto', []];
        } elseif ($context instanceof CashContext) {
            $context = ['cash', [$context->getStep()]];
        } elseif ($context instanceof DefaultContext) {
            $context = ['default', []];
        } else {
            $context = [];
        }

        return [[
            'amount' => (string) $target->getAmount(),
            'currency' => (string) $target->getCurrency(),
            'context' => $context,
        ], []];
    }

    /** @param array<string,mixed> $value */
    public function hydrate(array $value): Money
    {
        /** @var string $amount */
        $amount = $value['amount'];

        /** @var string $currency */
        $currency = $value['currency'];

        /** @var array<int,mixed> $context */
        $context = $value['context'] ?? [];

        /** @var string $contextType */
        $contextType = $context[0] ?? 'default';

        /** @var array<int> $contextArgs */
        $contextArgs = $context[1] ?? [];

        $contextInstance = match ($contextType) {
            'cash' => new CashContext(...$contextArgs),
            'custom' => new CustomContext(...$contextArgs),
            'auto' => new AutoContext,
            'default' => new DefaultContext,
            default => throw new MoneyMismatchException(__('brick-money::validation.invalid_context')),
        };

        return Money::of($amount, $currency, $contextInstance);
    }

    public function get(&$target, $key): string
    {
        if (! $target instanceof Money) {
            throw new MoneyMismatchException(__('brick-money::validation.unexpected_value', [
                'expected' => Money::class,
                'actual' => gettype($target),
            ]));
        }

        return match ($key) {
            'amount' => (string) $target->getAmount(),
            'currency' => (string) $target->getCurrency(),
            default => throw new MoneyMismatchException(__('brick-money::validation.invalid_property')),
        };
    }

    public function set(&$target, $key, $value): void
    {
        if (! $target instanceof Money) {
            throw new MoneyMismatchException(__('brick-money::validation.unexpected_value', [
                'expected' => Money::class,
                'actual' => gettype($target),
            ]));
        }

        if ($key === 'amount') {
            $target = Money::of($value, $target->getCurrency());
        } elseif ($key === 'currency') {
            $target = Money::of($target->getAmount(), $value);
        } else {
            throw new MoneyMismatchException(__('brick-money::validation.invalid_property'));
        }
    }
}
