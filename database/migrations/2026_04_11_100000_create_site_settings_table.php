<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_display_name')->nullable();
            $table->string('site_tagline')->nullable();
            $table->string('bg_home_path')->nullable();
            $table->string('bg_search_path')->nullable();
            $table->string('bg_login_path')->nullable();
            $table->string('bg_register_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
