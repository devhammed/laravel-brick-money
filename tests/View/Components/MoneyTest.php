<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('renders a money element', function () {
    $this->blade('<x-money amount="1000" />')->assertSee('$10.00');
    $this->blade('<x-money amount="5000" major />')->assertSee('$5,000.00');
    $this->blade('<x-money amount="2000" currency="EUR" major />')->assertSee('€2.000,00');
});
