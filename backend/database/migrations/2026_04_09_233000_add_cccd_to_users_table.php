<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'cccd')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('cccd', 20)->nullable()->after('phone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'cccd')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('cccd');
            });
        }
    }
};

