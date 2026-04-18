<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PasswordChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordChangeRequestController extends Controller
{
    public function index(): View
    {
        $requests = PasswordChangeRequest::query()
            ->with(['user:id,name,email', 'approver:id,name'])
            ->orderByRaw("case when status = 'pending' then 0 else 1 end")
            ->orderByDesc('requested_at')
            ->paginate(20);

        return view('admin.password-change-requests.index', compact('requests'));
    }

    public function approve(Request $request, PasswordChangeRequest $passwordChangeRequest): RedirectResponse
    {
        if ($passwordChangeRequest->status !== PasswordChangeRequest::STATUS_PENDING) {
            return back()->with('error', __('Yêu cầu này đã được xử lý.'));
        }

        $passwordChangeRequest->user->update([
            'password' => $passwordChangeRequest->new_password_hash,
        ]);

        $passwordChangeRequest->update([
            'status' => PasswordChangeRequest::STATUS_APPROVED,
            'approved_by' => $request->user()->id,
            'decided_at' => now(),
        ]);

        return back()->with('status', __('Đã duyệt yêu cầu đổi mật khẩu.'));
    }

    public function reject(Request $request, PasswordChangeRequest $passwordChangeRequest): RedirectResponse
    {
        if ($passwordChangeRequest->status !== PasswordChangeRequest::STATUS_PENDING) {
            return back()->with('error', __('Yêu cầu này đã được xử lý.'));
        }

        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $passwordChangeRequest->update([
            'status' => PasswordChangeRequest::STATUS_REJECTED,
            'approved_by' => $request->user()->id,
            'decided_at' => now(),
            'admin_note' => $data['admin_note'] ?? null,
        ]);

        return back()->with('status', __('Đã từ chối yêu cầu đổi mật khẩu.'));
    }
}

