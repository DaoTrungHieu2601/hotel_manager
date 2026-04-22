<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('policy_check_in_start', 5)->default('08:00')->after('social_enabled');
            $table->string('policy_check_in_end', 5)->default('08:30')->after('policy_check_in_start');
            $table->string('policy_check_out_start', 5)->default('10:00')->after('policy_check_in_end');
            $table->string('policy_check_out_end', 5)->default('11:00')->after('policy_check_out_start');
            $table->string('no_show_cutoff_time', 5)->default('23:30')->after('policy_check_out_end');
            $table->unsignedSmallInteger('check_time_grace_minutes')->default(15)->after('no_show_cutoff_time');
            $table->decimal('extra_hour_price', 12, 2)->default(100000)->after('check_time_grace_minutes');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('guest_planned_check_in', 5)->nullable()->after('guest_notes');
            $table->string('guest_planned_check_out', 5)->nullable()->after('guest_planned_check_in');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('early_late_subtotal', 12, 2)->default(0)->after('services_subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('early_late_subtotal');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['guest_planned_check_in', 'guest_planned_check_out']);
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'policy_check_in_start',
                'policy_check_in_end',
                'policy_check_out_start',
                'policy_check_out_end',
                'no_show_cutoff_time',
                'check_time_grace_minutes',
                'extra_hour_price',
            ]);
        });
    }
};
