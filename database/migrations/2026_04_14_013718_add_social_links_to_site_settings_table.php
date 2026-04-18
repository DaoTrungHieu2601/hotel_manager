<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('social_facebook')->nullable()->after('chatbot_system_prompt');
            $table->string('social_zalo')->nullable()->after('social_facebook');
            $table->string('social_phone')->nullable()->after('social_zalo');
            $table->string('social_instagram')->nullable()->after('social_phone');
            $table->boolean('social_enabled')->default(true)->after('social_instagram');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['social_facebook', 'social_zalo', 'social_phone', 'social_instagram', 'social_enabled']);
        });
    }
};
