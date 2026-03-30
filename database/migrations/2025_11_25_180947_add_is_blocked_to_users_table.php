<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة حقل is_blocked إلى جدول users
            $table->boolean('is_blocked')
                  ->default(false)
                  ->after('role'); // ييجي بعد role
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // حذف العمود لو عملنا rollback
            $table->dropColumn('is_blocked');
        });
    }
};
