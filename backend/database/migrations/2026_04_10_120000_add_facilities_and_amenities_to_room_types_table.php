<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->text('facilities')->nullable()->after('description');
            $table->text('amenities')->nullable()->after('facilities');
        });
    }

    public function down(): void
    {
        Schema::table('room_types', function (Blueprint $table) {
            $table->dropColumn(['facilities', 'amenities']);
        });
    }
};
