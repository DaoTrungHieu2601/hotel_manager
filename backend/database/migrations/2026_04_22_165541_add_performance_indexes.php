<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'bookings_user_status_idx');
            $table->index(['check_in', 'check_out'], 'bookings_dates_idx');
            $table->index('status', 'bookings_status_idx');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->index(['chat_conversation_id', 'created_at'], 'chat_messages_conv_time_idx');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->index('status', 'rooms_status_idx');
            $table->index('room_type_id', 'rooms_room_type_idx');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_user_status_idx');
            $table->dropIndex('bookings_dates_idx');
            $table->dropIndex('bookings_status_idx');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_messages_conv_time_idx');
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->dropIndex('rooms_status_idx');
            $table->dropIndex('rooms_room_type_idx');
        });
    }
};
