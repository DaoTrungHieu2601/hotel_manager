<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class StaffController extends Controller
{
    public function index(): View
    {
        $staff = User::query()
            ->whereIn('role', User::STAFF_ROLES)
            ->orderBy('name')
            ->paginate(20);

        return view('admin.staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('admin.staff.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(User::STAFF_ROLES)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::query()->create($data);

        return redirect()->route('admin.staff.index')->with('status', __('Đã tạo tài khoản.'));
    }

    public function edit(User $staff): View
    {
        abort_unless(in_array($staff->role, User::STAFF_ROLES, true), 404);

        return view('admin.staff.edit', ['user' => $staff]);
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        abort_unless(in_array($staff->role, User::STAFF_ROLES, true), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($staff->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(User::STAFF_ROLES)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')->with('status', __('Đã cập nhật.'));
    }

    public function destroy(Request $request, User $staff): RedirectResponse
    {
        abort_unless(in_array($staff->role, User::STAFF_ROLES, true), 404);

        if ($staff->id === $request->user()->id) {
            return redirect()->route('admin.staff.index')->with('error', __('Không thể xóa chính mình.'));
        }

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('status', __('Đã xóa.'));
    }
}
