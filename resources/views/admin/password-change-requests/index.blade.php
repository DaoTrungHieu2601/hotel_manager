<x-hotel-layout>
    <x-slot name="header">{{ __('Yêu cầu đổi mật khẩu') }}</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-900 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="px-5 py-4">#</th>
                    <th class="px-5 py-4">{{ __('Lễ tân') }}</th>
                    <th class="px-5 py-4">{{ __('Thời gian gửi') }}</th>
                    <th class="px-5 py-4">{{ __('Trạng thái') }}</th>
                    <th class="px-5 py-4 text-right">{{ __('Thao tác') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $r)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-semibold text-gray-900">{{ $r->id }}</td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-gray-900">{{ $r->user?->name }}</p>
                            <p class="text-xs text-gray-600">{{ $r->user?->email }}</p>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $r->requested_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-5 py-4">
                            @php
                                $statusClass = match($r->status) {
                                    \App\Models\PasswordChangeRequest::STATUS_PENDING => 'bg-amber-100 text-amber-800 ring-amber-200',
                                    \App\Models\PasswordChangeRequest::STATUS_APPROVED => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
                                    \App\Models\PasswordChangeRequest::STATUS_REJECTED => 'bg-rose-100 text-rose-800 ring-rose-200',
                                    default => 'bg-slate-100 text-slate-700 ring-slate-200',
                                };
                            @endphp
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClass }}">
                                {{ $r->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @if($r->status === \App\Models\PasswordChangeRequest::STATUS_PENDING)
                                <div class="flex justify-end gap-2">
                                    <form method="post" action="{{ route('admin.password-change-requests.approve', $r) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex rounded-full bg-emerald-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-emerald-500">{{ __('Duyệt') }}</button>
                                    </form>
                                    <form method="post" action="{{ route('admin.password-change-requests.reject', $r) }}">
                                        @csrf
                                        <input type="hidden" name="admin_note" value="{{ __('Không được phê duyệt.') }}" />
                                        <button type="submit" class="inline-flex rounded-full bg-rose-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-rose-500">{{ __('Từ chối') }}</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-600">
                                    {{ $r->approver?->name ? __('Xử lý bởi: :name', ['name' => $r->approver->name]) : '—' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-600">{{ __('Chưa có yêu cầu đổi mật khẩu nào.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $requests->links() }}
    </div>
</x-hotel-layout>

