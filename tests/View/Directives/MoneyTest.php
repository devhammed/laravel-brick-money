<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('renders a money element', function () {
    $this->blade('@money(1000)')->assertSee('$10.00');
});

it('renders a money element with a major unit', function () {
    $this->blade('@money(5000, major: true)')->assertSee('$5,000.00');
});

it('renders a money element with different currency', function () {
    $this->blade('@money(2000, "EUR", major: true)')->assertSee('€2.000,00');
});
