<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\PasswordChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isStaff(), 403);

        $data = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        PasswordChangeRequest::query()
            ->where('user_id', $user->id)
            ->where('status', PasswordChangeRequest::STATUS_PENDING)
            ->delete();

        PasswordChangeRequest::query()->create([
            'user_id' => $user->id,
            'new_password_hash' => Hash::make($data['password']),
            'status' => PasswordChangeRequest::STATUS_PENDING,
            'requested_at' => now(),
        ]);

        return back()->with('status', 'password-change-requested');
    }
}

