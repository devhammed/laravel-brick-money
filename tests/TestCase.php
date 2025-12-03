<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests;

use Devhammed\LaravelBrickMoney\Provider;
use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            Provider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['db']->connection()->getSchemaBuilder()->create('transactions', function (Blueprint $table) {
            $table->id();

            $table->string('price');
            $table->string('price_currency');
            $table->json('tax');
            $table->json('gas_fee');
            $table->decimal('platform_fee', 40, 20);
            $table->string('platform_fee_currency');

            $table->timestamps();
        });
    }
}
