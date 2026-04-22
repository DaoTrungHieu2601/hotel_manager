<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('default_price', 12, 2);
            $table->unsignedTinyInteger('beds')->default(1);
            $table->unsignedTinyInteger('max_occupancy')->default(2);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('floor')->nullable();
            $table->string('status', 32)->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->unsignedSmallInteger('guests')->default(1);
            $table->string('status', 32)->default('pending');
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->decimal('rate_per_night', 12, 2)->nullable();
            $table->text('guest_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();
        });

        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->unsignedSmallInteger('nights');
            $table->decimal('room_subtotal', 12, 2);
            $table->decimal('services_subtotal', 12, 2)->default(0);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('booking_services');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('services');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_types');
    }
};
