<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('name', 120);
            $table->boolean('is_customer')->default(false);
            $table->boolean('is_staff')->default(true);
            $table->boolean('can_access_admin')->default(false);
            $table->boolean('can_access_reception')->default(false);
            $table->boolean('notify_reception_ops')->default(false);
            $table->boolean('notify_pending_customer_booking')->default(false);
            $table->json('default_permissions')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $all = [
            'admin_dashboard', 'reservations', 'messages', 'password_requests',
            'room_map', 'check_in_out', 'site_settings', 'room_types', 'hotel_rooms',
            'services', 'staff_management', 'customers', 'permissions', 'invoices',
        ];

        $recv = ['room_map', 'check_in_out', 'reservations', 'invoices', 'messages'];
        $mgr = [
            'admin_dashboard', 'reservations', 'room_map', 'check_in_out', 'messages',
            'invoices', 'room_types', 'hotel_rooms', 'services', 'customers',
        ];
        $acc = ['invoices', 'reservations', 'admin_dashboard'];

        $now = now();
        $rows = [
            [
                'slug' => 'admin', 'name' => 'Quản trị viên', 'is_customer' => false, 'is_staff' => true,
                'can_access_admin' => true, 'can_access_reception' => true, 'notify_reception_ops' => true, 'notify_pending_customer_booking' => true,
                'default_permissions' => json_encode($all), 'sort_order' => 10,
            ],
            [
                'slug' => 'director', 'name' => 'Giám đốc', 'is_customer' => false, 'is_staff' => true,
                'can_access_admin' => true, 'can_access_reception' => true, 'notify_reception_ops' => true, 'notify_pending_customer_booking' => true,
                'default_permissions' => json_encode($all), 'sort_order' => 20,
            ],
            [
                'slug' => 'receptionist', 'name' => 'Nhân viên', 'is_customer' => false, 'is_staff' => true,
                'can_access_admin' => false, 'can_access_reception' => true, 'notify_reception_ops' => false, 'notify_pending_customer_booking' => true,
                'default_permissions' => json_encode($recv), 'sort_order' => 30,
            ],
            [
                'slug' => 'manager', 'name' => 'Trưởng phòng', 'is_customer' => false, 'is_staff' => true,
                'can_access_admin' => false, 'can_access_reception' => true, 'notify_reception_ops' => false, 'notify_pending_customer_booking' => false,
                'default_permissions' => json_encode($mgr), 'sort_order' => 40,
            ],
            [
                'slug' => 'accountant', 'name' => 'Kế toán', 'is_customer' => false, 'is_staff' => true,
                'can_access_admin' => false, 'can_access_reception' => true, 'notify_reception_ops' => false, 'notify_pending_customer_booking' => false,
                'default_permissions' => json_encode($acc), 'sort_order' => 50,
            ],
            [
                'slug' => 'customer', 'name' => 'Khách hàng', 'is_customer' => true, 'is_staff' => false,
                'can_access_admin' => false, 'can_access_reception' => false, 'notify_reception_ops' => false, 'notify_pending_customer_booking' => false,
                'default_permissions' => json_encode([]), 'sort_order' => 90,
            ],
        ];

        foreach ($rows as $r) {
            $r['created_at'] = $now;
            $r['updated_at'] = $now;
            DB::table('system_roles')->insert($r);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_roles');
    }
};
