<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Filament\Tables\Columns;

use Devhammed\LaravelBrickMoney\Money;
use Filament\Tables\Columns\TextColumn;

class MoneyColumn extends TextColumn
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(fn (?Money $state): ?string => $state?->format(true));
    }
}
