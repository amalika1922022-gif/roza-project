<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('qty');
            $table->string('status')->default('reserved'); // reserved | committed | released
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'product_id']); // يمنع تكرار الحجز لنفس المنتج بنفس الطلب
            $table->index(['status', 'expires_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_reservations');
    }
};
