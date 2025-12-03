<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;

uses(InteractsWithViews::class);

it('can render currency', function () {
    $this->blade('<x-currency currency="USD" />')
        ->assertSee('USD');
});
