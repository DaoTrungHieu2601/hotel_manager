<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('site_address')->nullable()->after('site_tagline');
            $table->string('site_phone')->nullable()->after('site_address');
            $table->string('site_email')->nullable()->after('site_phone');
            $table->string('site_website')->nullable()->after('site_email');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['site_address', 'site_phone', 'site_email', 'site_website']);
        });
    }
};
