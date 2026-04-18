<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('chatbot_enabled')->default(false)->after('site_website');
            $table->string('chatbot_name', 80)->nullable()->after('chatbot_enabled');
            $table->string('chatbot_avatar_path')->nullable()->after('chatbot_name');
            $table->string('chatbot_gemini_api_key', 200)->nullable()->after('chatbot_avatar_path');
            $table->text('chatbot_system_prompt')->nullable()->after('chatbot_gemini_api_key');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'chatbot_enabled',
                'chatbot_name',
                'chatbot_avatar_path',
                'chatbot_gemini_api_key',
                'chatbot_system_prompt',
            ]);
        });
    }
};
