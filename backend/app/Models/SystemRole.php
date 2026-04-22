<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemRole extends Model
{
    protected $table = 'system_roles';

    protected $fillable = [
        'slug',
        'name',
        'is_customer',
        'is_staff',
        'can_access_admin',
        'can_access_reception',
        'notify_reception_ops',
        'notify_pending_customer_booking',
        'default_permissions',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_customer' => 'boolean',
            'is_staff' => 'boolean',
            'can_access_admin' => 'boolean',
            'can_access_reception' => 'boolean',
            'notify_reception_ops' => 'boolean',
            'notify_pending_customer_booking' => 'boolean',
            'default_permissions' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'slug');
    }

    /** Vai trò có thể gán cho nhân viên (form nhân sự) */
    public static function staffAssignable()
    {
        return static::query()->where('is_staff', true)->orderBy('sort_order')->orderBy('name');
    }
}
