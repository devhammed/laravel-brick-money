<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
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
};
