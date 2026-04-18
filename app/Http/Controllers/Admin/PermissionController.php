<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    private const ALLOWED_ROLES = [
        User::ROLE_ADMIN,
        User::ROLE_RECEPTIONIST,
        User::ROLE_MANAGER,
        User::ROLE_ACCOUNTANT,
        User::ROLE_CUSTOMER,
    ];

    public function index(Request $request): View
    {
        $search     = $request->query('search', '');
        $roleFilter = $request->query('role', '');

        $query = User::query()->orderBy('role')->orderBy('name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== '' && in_array($roleFilter, self::ALLOWED_ROLES, true)) {
            $query->where('role', $roleFilter);
        }

        $users = $query->paginate(20)->withQueryString();

        $stats = [
            User::ROLE_ADMIN        => User::where('role', User::ROLE_ADMIN)->count(),
            User::ROLE_RECEPTIONIST => User::where('role', User::ROLE_RECEPTIONIST)->count(),
            User::ROLE_MANAGER      => User::where('role', User::ROLE_MANAGER)->count(),
            User::ROLE_ACCOUNTANT   => User::where('role', User::ROLE_ACCOUNTANT)->count(),
            User::ROLE_CUSTOMER     => User::where('role', User::ROLE_CUSTOMER)->count(),
        ];

        $allPerms = User::permissionLabels();

        return view('admin.permissions.index', compact('users', 'stats', 'search', 'roleFilter', 'allPerms'));
    }

    /** Đổi role nhanh */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', __('Không thể thay đổi quyền của chính bạn.'));
        }

        $data = $request->validate([
            'role' => ['required', 'in:' . implode(',', self::ALLOWED_ROLES)],
        ]);

        $oldRole = $user->role;
        // Khi đổi role → reset permissions về null (dùng mặc định của role mới)
        $user->update(['role' => $data['role'], 'permissions' => null]);

        $roleLabels = User::roleLabels();

        return back()->with('status', "Đã đổi quyền của {$user->name} từ «{$roleLabels[$oldRole]}» → «{$roleLabels[$data['role']]}».");
    }

    /** Lưu permissions chi tiết cho staff */
    public function updatePermissions(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Không thể chỉnh sửa quyền của chính bạn.');
        }

        if ($user->isCustomer()) {
            return back()->with('error', 'Không thể phân quyền chi tiết cho khách hàng.');
        }

        $allKeys    = array_keys(User::permissionLabels());
        $submitted  = $request->input('perms', []);

        // Chỉ giữ lại các key hợp lệ
        $granted = array_values(array_filter($submitted, fn($k) => in_array($k, $allKeys, true)));

        // Nếu giống mặc định của role → lưu null (clean)
        $defaults = User::defaultPermissionsForRole($user->role);
        sort($granted);
        sort($defaults);
        $permissions = ($granted === $defaults) ? null : $granted;

        $user->update(['permissions' => $permissions]);

        return back()->with('status', "Đã cập nhật quyền chi tiết cho {$user->name}.");
    }

    /** Reset về mặc định của role */
    public function resetPermissions(User $user): RedirectResponse
    {
        $user->update(['permissions' => null]);

        return back()->with('status', "Đã đặt lại quyền mặc định cho {$user->name}.");
    }

    /** Cũ - giữ lại để tương thích (redirect sang updateRole) */
    public function update(Request $request, User $user): RedirectResponse
    {
        return $this->updateRole($request, $user);
    }
}
