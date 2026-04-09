<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney;

use Brick\Math\BigNumber;
use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Devhammed\LaravelBrickMoney\Livewire\Synthesizers\CurrencySynthesizer;
use Devhammed\LaravelBrickMoney\Livewire\Synthesizers\MoneySynthesizer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Request;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class Provider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-brick-money')
            ->hasViews()
            ->hasConfigFile()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        Blade::directive('money', function (string $expression) {
            return "<?php echo money({$expression}); ?>";
        });

        Blade::directive('currency', function (string $expression) {
            return "<?php echo currency({$expression}); ?>";
        });

        Blade::component('money', View\Components\Money::class);

        Blade::component('currency', View\Components\Currency::class);

        Money::locale($this->app->make('translator')->getLocale());

        /** @var array<mixed> $currencies */
        $currencies = $this->app->make('config')->get('brick-money.currencies', []);

        /** @var bool $minor */
        $minor = $this->app->make('config')->get('brick-money.minor');

        Currency::currencies($currencies);

        Money::jsonSerializeMinorUnits($minor);

        Request::macro('currency', function (string $key, ?string $default = null): ?Currency {
            $value = $this->input($key, $default);

            if (! is_string($value)) {
                return null;
            }

            return currency($value);
        });

        Request::macro('money', function (
            string $key,
            BigNumber|string|int|float|null $default = null,
            ?string $currency = null,
            ?bool $minor = null,
            ?Context $context = null,
            RoundingMode $roundingMode = RoundingMode::Unnecessary
        ): ?Money {
            $value = $this->input($key, $default);

            if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
                return null;
            }

            return money($value, $currency, $minor, $context, $roundingMode);
        });

        if (class_exists(Livewire::class)) {
            Livewire::propertySynthesizer(MoneySynthesizer::class);
            Livewire::propertySynthesizer(CurrencySynthesizer::class);
        }
    }
}
