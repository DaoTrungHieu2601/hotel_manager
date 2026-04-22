<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone'    => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => User::ROLE_CUSTOMER,
            'phone'    => $data['phone'] ?? null,
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $this->userResource($user),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($data)) {
            return response()->json(['message' => 'Email hoặc mật khẩu không đúng.'], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => $this->userResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Đăng xuất thành công.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($this->userResource($request->user()));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'    => ['sometimes', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update($data);

        return response()->json($this->userResource($user->fresh()));
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::defaults()],
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return response()->json(['message' => 'Mật khẩu hiện tại không đúng.'], 422);
        }

        $request->user()->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Đổi mật khẩu thành công.']);
    }

    private function userResource(User $user): array
    {
        return [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'role'        => $user->role,
            'phone'       => $user->phone,
            'address'     => $user->address,
            'cccd'        => $user->cccd,
            'permissions' => $user->effectivePermissions(),
            'can_access_admin'     => $user->canAccessAdmin(),
            'can_access_reception' => $user->canAccessReception(),
            'is_customer'          => $user->isCustomer(),
            'created_at'  => $user->created_at,
        ];
    }
}
