<?php

namespace Devhammed\LaravelBrickMoney\View\Components;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Currency extends Component
{
    public function __construct(public string $currency)
    {
        //
    }

    public function render(): View|Factory
    {
        return view('brick-money::components.currency');
    }
}
