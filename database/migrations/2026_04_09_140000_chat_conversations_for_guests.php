<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('guest_key', 64)->nullable()->unique();
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_id')->nullable()->change();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreign('sender_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_id')->nullable(false)->change();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->foreign('sender_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropUnique(['guest_key']);
            $table->dropColumn('guest_key');
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->unique('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
