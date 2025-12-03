<?php

declare(strict_types=1);

namespace Devhammed\LaravelBrickMoney\Tests\Eloquent\Factories;

use Devhammed\LaravelBrickMoney\Money;
use Devhammed\LaravelBrickMoney\Tests\Eloquent\Models\TransactionModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionModel>
 */
class TransactionModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = TransactionModel::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'price' => Money::of($this->faker->numberBetween(10, 99999), $this->faker->randomElement(['USD', 'EUR', 'NGN'])),
            'tax' => Money::of($this->faker->numberBetween(10, 99999), $this->faker->randomElement(['USD', 'EUR', 'NGN'])),
            'platform_fee' => Money::of($this->faker->numberBetween(10, 99999), $this->faker->randomElement(['USD', 'EUR', 'NGN'])),
            'gas_fee' => Money::of($this->faker->numberBetween(10, 99999), $this->faker->randomElement(['USD', 'EUR', 'NGN'])),
        ];
    }
}
