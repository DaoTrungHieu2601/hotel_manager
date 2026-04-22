<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('employee')->after('password');
            $table->foreignId('department_id')->nullable()->after('role')->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->after('department_id')->constrained()->nullOnDelete();
            $table->string('phone', 30)->nullable()->after('position_id');
            $table->text('address')->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['position_id']);
            $table->dropColumn([
                'role',
                'department_id',
                'position_id',
                'phone',
                'address',
                'birth_date',
            ]);
        });
    }
};
