<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemRole;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('systemRole', 'position')
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->orderBy('name')
            ->paginate(20);

        return response()->json([
            'users'            => $users,
            'system_roles'     => SystemRole::orderBy('sort_order')->get(),
            'permission_labels'=> User::permissionLabels(),
        ]);
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'exists:system_roles,slug'],
        ]);

        $user->update(['role' => $request->role, 'permissions' => null]);

        return response()->json($user->fresh()->load('systemRole'));
    }

    public function updatePermissions(Request $request, User $user): JsonResponse
    {
        $valid = array_keys(User::permissionLabels());

        $request->validate([
            'permissions'   => ['present', 'array'],
            'permissions.*' => ['string', 'in:' . implode(',', $valid)],
        ]);

        $user->update(['permissions' => $request->permissions]);

        return response()->json($user->fresh());
    }

    public function resetPermissions(User $user): JsonResponse
    {
        $user->update(['permissions' => null]);

        return response()->json($user->fresh());
    }

    public function storeSystemRole(Request $request): JsonResponse
    {
        $data = $request->validate([
            'slug'                           => ['required', 'string', 'unique:system_roles,slug'],
            'name'                           => ['required', 'string'],
            'is_customer'                    => ['boolean'],
            'is_staff'                       => ['boolean'],
            'can_access_admin'               => ['boolean'],
            'can_access_reception'           => ['boolean'],
            'notify_reception_ops'           => ['boolean'],
            'notify_pending_customer_booking'=> ['boolean'],
            'default_permissions'            => ['array'],
            'sort_order'                     => ['integer'],
        ]);

        return response()->json(SystemRole::create($data), 201);
    }
}
