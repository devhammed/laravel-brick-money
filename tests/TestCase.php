<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests;

use Devhammed\LaravelBrickMoney\Provider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Model::unguard();

        Config::set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    protected function getPackageProviders($app): array
    {
        return [
            Provider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        //
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Eloquent/Migrations');
    }
}
