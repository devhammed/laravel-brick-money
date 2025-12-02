<?php

namespace Devhammed\LaravelBrickMoney\View\Components;

use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Money extends Component
{
    public function __construct(
        public BigNumber|float|int|string $amount,
        public ?string $currency = null,
        public ?bool $major = null,
        public ?Context $context = null,
        public RoundingMode $roundingMode = RoundingMode::UNNECESSARY
    ) {
        //
    }

    public function render(): View|Factory
    {
        return view('brick-money::components.money');
    }
}
