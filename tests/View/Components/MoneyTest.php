<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('renders a money element', function () {
    $this->blade('<x-money amount="1000" minor />')->assertSee('$10');
});

it('renders a money element with a major unit', function () {
    $this->blade('<x-money amount="5000" />')->assertSee('$5,000');
});

it('renders a money element with different currency', function () {
    $this->blade('<x-money amount="2000" currency="EUR" />')->assertSee('€2.000');
});
