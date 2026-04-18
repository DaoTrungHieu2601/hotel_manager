<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN        = 'admin';
    public const ROLE_RECEPTIONIST = 'receptionist'; // Nhân viên
    public const ROLE_MANAGER      = 'manager';       // Trưởng phòng
    public const ROLE_ACCOUNTANT   = 'accountant';    // Kế toán
    public const ROLE_CUSTOMER     = 'customer';

    /** Tất cả role nhân viên (không phải khách hàng) */
    public const STAFF_ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_RECEPTIONIST,
        self::ROLE_MANAGER,
        self::ROLE_ACCOUNTANT,
    ];

    /** Map role → nhãn hiển thị tiếng Việt */
    public static function roleLabels(): array
    {
        return [
            self::ROLE_ADMIN        => 'Quản trị viên',
            self::ROLE_RECEPTIONIST => 'Nhân viên',
            self::ROLE_MANAGER      => 'Trưởng phòng',
            self::ROLE_ACCOUNTANT   => 'Kế toán',
            self::ROLE_CUSTOMER     => 'Khách hàng',
        ];
    }

    // ── Danh sách tất cả permissions ───────────────────────────────────
    public const PERM_ADMIN_DASHBOARD  = 'admin_dashboard';
    public const PERM_RESERVATIONS     = 'reservations';
    public const PERM_MESSAGES         = 'messages';
    public const PERM_PASSWORD_REQUESTS= 'password_requests';
    public const PERM_ROOM_MAP         = 'room_map';
    public const PERM_CHECK_IN_OUT     = 'check_in_out';
    public const PERM_SITE_SETTINGS    = 'site_settings';
    public const PERM_ROOM_TYPES       = 'room_types';
    public const PERM_HOTEL_ROOMS      = 'hotel_rooms';
    public const PERM_SERVICES         = 'services';
    public const PERM_STAFF_MANAGEMENT = 'staff_management';
    public const PERM_CUSTOMERS        = 'customers';
    public const PERM_PERMISSIONS      = 'permissions';
    public const PERM_INVOICES         = 'invoices';

    /** Nhãn hiển thị của từng permission */
    public static function permissionLabels(): array
    {
        return [
            self::PERM_ADMIN_DASHBOARD   => ['label' => 'Báo cáo Admin',                'group' => 'admin',      'icon' => '📊'],
            self::PERM_RESERVATIONS      => ['label' => 'Quản lý đặt phòng',             'group' => 'admin',      'icon' => '📋'],
            self::PERM_MESSAGES          => ['label' => 'Tin nhắn khách hàng',           'group' => 'admin',      'icon' => '💬'],
            self::PERM_PASSWORD_REQUESTS => ['label' => 'Yêu cầu đổi mật khẩu',         'group' => 'admin',      'icon' => '🔐'],
            self::PERM_ROOM_MAP          => ['label' => 'Sơ đồ phòng',                   'group' => 'reception',  'icon' => '🏨'],
            self::PERM_CHECK_IN_OUT      => ['label' => 'Check-in / Check-out',          'group' => 'reception',  'icon' => '✅'],
            self::PERM_INVOICES          => ['label' => 'Hóa đơn & Xuất PDF',            'group' => 'reception',  'icon' => '🧾'],
            self::PERM_SITE_SETTINGS     => ['label' => 'Cài đặt website',               'group' => 'settings',   'icon' => '⚙️'],
            self::PERM_ROOM_TYPES        => ['label' => 'Loại phòng & giá',              'group' => 'settings',   'icon' => '🛏️'],
            self::PERM_HOTEL_ROOMS       => ['label' => 'Danh sách phòng',               'group' => 'settings',   'icon' => '🚪'],
            self::PERM_SERVICES          => ['label' => 'Dịch vụ',                       'group' => 'settings',   'icon' => '🍽️'],
            self::PERM_STAFF_MANAGEMENT  => ['label' => 'Nhân sự / Lễ tân',             'group' => 'settings',   'icon' => '👥'],
            self::PERM_CUSTOMERS         => ['label' => 'Tài khoản khách hàng',          'group' => 'settings',   'icon' => '👤'],
            self::PERM_PERMISSIONS       => ['label' => 'Phân quyền người dùng',         'group' => 'settings',   'icon' => '🔑'],
        ];
    }

    /** Quyền mặc định theo role (khi permissions = null) */
    public static function defaultPermissionsForRole(string $role): array
    {
        return match ($role) {
            self::ROLE_ADMIN => array_keys(self::permissionLabels()),
            self::ROLE_RECEPTIONIST => [
                self::PERM_ROOM_MAP,
                self::PERM_CHECK_IN_OUT,
                self::PERM_RESERVATIONS,
                self::PERM_INVOICES,
                self::PERM_MESSAGES,
            ],
            self::ROLE_MANAGER => [
                self::PERM_ADMIN_DASHBOARD,
                self::PERM_RESERVATIONS,
                self::PERM_ROOM_MAP,
                self::PERM_CHECK_IN_OUT,
                self::PERM_MESSAGES,
                self::PERM_INVOICES,
                self::PERM_ROOM_TYPES,
                self::PERM_HOTEL_ROOMS,
                self::PERM_SERVICES,
                self::PERM_CUSTOMERS,
            ],
            self::ROLE_ACCOUNTANT => [
                self::PERM_INVOICES,
                self::PERM_RESERVATIONS,
                self::PERM_ADMIN_DASHBOARD,
            ],
            default => [],
        };
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'phone',
        'cccd',
        'address',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'permissions'       => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isReceptionist(): bool
    {
        return $this->role === self::ROLE_RECEPTIONIST;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isAccountant(): bool
    {
        return $this->role === self::ROLE_ACCOUNTANT;
    }

    /** Trả về true nếu là bất kỳ nhân viên nào (không phải khách hàng) */
    public function isStaff(): bool
    {
        return in_array($this->role, self::STAFF_ROLES, true);
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Kiểm tra user có permission cụ thể không.
     * - Nếu permissions = null → dùng quyền mặc định của role
     * - Nếu permissions đã set → kiểm tra trong mảng đó
     * - Customer luôn trả về false cho mọi staff permission
     */
    public function hasPermission(string $perm): bool
    {
        if ($this->isCustomer()) {
            return false;
        }

        $granted = $this->permissions;

        if ($granted === null) {
            return in_array($perm, self::defaultPermissionsForRole($this->role), true);
        }

        return in_array($perm, $granted, true);
    }

    /** Trả về mảng permissions đang áp dụng (mặc định hoặc tùy chỉnh) */
    public function effectivePermissions(): array
    {
        return $this->permissions ?? self::defaultPermissionsForRole($this->role);
    }

    /** Permissions đã được tùy chỉnh (khác mặc định) ? */
    public function hasCustomPermissions(): bool
    {
        return $this->permissions !== null;
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function chatConversation(): HasOne
    {
        return $this->hasOne(ChatConversation::class);
    }

    public function passwordChangeRequests(): HasMany
    {
        return $this->hasMany(PasswordChangeRequest::class);
    }
}
