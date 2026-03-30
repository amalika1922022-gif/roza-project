<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->string('provider', 50)->default('stripe');

            // Stripe PaymentIntent ID (pi_***)
            $table->string('provider_payment_id', 255)->nullable()->unique();

            // Stripe real status (requires_payment_method, succeeded, ...)
            $table->string('provider_status', 100)->nullable();

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');

            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])
                ->default('pending');

            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
