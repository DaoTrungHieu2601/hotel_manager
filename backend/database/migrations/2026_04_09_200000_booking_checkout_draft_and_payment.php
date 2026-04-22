<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('deposit_paid_at')->nullable()->after('deposit_amount');
            $table->string('payment_method', 32)->nullable()->after('deposit_paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['deposit_paid_at', 'payment_method']);
        });
    }
};
