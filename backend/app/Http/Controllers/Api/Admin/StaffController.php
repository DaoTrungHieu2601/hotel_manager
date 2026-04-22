<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreStaffRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $staff = User::with('systemRole', 'position')
            ->whereIn('role', User::staffRoleSlugs())
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->orderBy('name')
            ->paginate(20);

        return response()->json($staff);
    }

    public function store(StoreStaffRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
        ]);

        return response()->json($user->load('systemRole', 'position'), 201);
    }

    public function show(User $staff): JsonResponse
    {
        return response()->json($staff->load('systemRole', 'position'));
    }

    public function update(StoreStaffRequest $request, User $staff): JsonResponse
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $staff->update($data);

        return response()->json($staff->fresh()->load('systemRole', 'position'));
    }

    public function destroy(Request $request, User $staff): JsonResponse
    {
        if ($staff->id === $request->user()->id) {
            return response()->json(['message' => 'Không thể xóa tài khoản đang đăng nhập.'], 422);
        }

        $staff->delete();

        return response()->json(null, 204);
    }
}
