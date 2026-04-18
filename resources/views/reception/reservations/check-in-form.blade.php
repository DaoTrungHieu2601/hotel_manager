<x-hotel-layout>
    <x-slot name="header">Bổ sung thông tin check-in</x-slot>

    @if ($errors->any())
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-900 shadow-sm">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mx-auto max-w-2xl rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Xác nhận thông tin khách hàng trước khi check-in</h2>
        <p class="mt-1 text-sm text-gray-600">Cần điền đầy đủ số điện thoại và số CCCD để hoàn tất check-in.</p>

        <dl class="mt-4 grid gap-2 text-sm">
            <div class="flex justify-between gap-4"><dt class="text-gray-600">Đơn</dt><dd class="font-medium text-gray-900">#{{ $booking->id }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-600">Khách</dt><dd class="text-gray-900">{{ $booking->user->name }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-600">Phòng</dt><dd class="text-gray-900">{{ $booking->room?->code ?? '?' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-600">Ngày ở</dt><dd class="text-gray-900">{{ $booking->check_in->format('d/m/Y') }} - {{ $booking->check_out->format('d/m/Y') }}</dd></div>
        </dl>
        <div
            class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900"
            data-early-fee-box
            data-check-in-date="{{ $booking->check_in->toDateString() }}"
            data-reference-check-in-time="{{ $referenceCheckInTime ?? $siteSetting->policy_check_in_start }}"
            data-extra-hour-price="{{ (float) ($siteSetting->extra_hour_price ?? 0) }}"
        >
            <p class="font-semibold" data-early-fee-title>Phụ phí check-in tạm tính: {{ number_format((float) ($checkInSurcharge ?? 0), 0, ',', '.') }} VND.</p>
            <p class="mt-1" data-early-fee-note>
                Không phát sinh phụ phí đến sớm tại thời điểm hiện tại (mốc so sánh {{ $referenceCheckInTime ?? $siteSetting->policy_check_in_start }}).
            </p>
        </div>

        <form method="post" action="{{ route('reception.stays.check-in-complete', $booking) }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="client_checked_in_at" id="client_checked_in_at" value="" />
            <input type="hidden" name="effective_planned_check_in" id="effective_planned_check_in" value="{{ $referenceCheckInTime ?? $siteSetting->policy_check_in_start }}" />
            <div>
                <label for="reference_check_in_time" class="mb-1 block text-sm font-medium text-gray-800">Giờ khách đến</label>
                <input
                    id="reference_check_in_time"
                    type="time"
                    step="60"
                    value="{{ $referenceCheckInTime ?? $siteSetting->policy_check_in_start }}"
                    class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                />
            </div>
            <div>
                <label for="phone" class="mb-1 block text-sm font-medium text-gray-800">Số điện thoại</label>
                <input
                    id="phone"
                    name="phone"
                    type="text"
                    value="{{ old('phone', $booking->user->phone) }}"
                    class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                    required
                />
            </div>
            <div>
                <label for="cccd" class="mb-1 block text-sm font-medium text-gray-800">Số CCCD</label>
                <input
                    id="cccd"
                    name="cccd"
                    type="text"
                    value="{{ old('cccd', $booking->user->cccd) }}"
                    placeholder="9-12 chữ số"
                    class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                    required
                />
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="rounded-full bg-emerald-600 px-6 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                    Hoàn tất check-in
                </button>
                <a href="{{ route('reception.reservations.show', $booking) }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 hover:underline">
                    Quay lại
                </a>
            </div>
        </form>
    </div>
    <script>
        (() => {
            const feeBox = document.querySelector('[data-early-fee-box]');
            const checkedInAtInput = document.getElementById('client_checked_in_at');
            const effectivePlannedInput = document.getElementById('effective_planned_check_in');
            const referenceInput = document.getElementById('reference_check_in_time');
            const form = document.querySelector('form[action*="check-in-complete"]');
            if (!feeBox) {
                return;
            }

            const titleEl = feeBox.querySelector('[data-early-fee-title]');
            const noteEl = feeBox.querySelector('[data-early-fee-note]');
            if (!titleEl || !noteEl) {
                return;
            }

            const checkInDate = feeBox.getAttribute('data-check-in-date') || '';
            const defaultReferenceTime = feeBox.getAttribute('data-reference-check-in-time') || '08:00';
            const extraHourPrice = Number.parseFloat(feeBox.getAttribute('data-extra-hour-price') || '0');
            if (!checkInDate) {
                return;
            }

            const formatCurrency = (value) => new Intl.NumberFormat('vi-VN').format(value);

            const render = () => {
                const now = new Date();
                const referenceTime = (referenceInput?.value || defaultReferenceTime || '08:00');
                const [refHourText, refMinuteText] = referenceTime.split(':');
                const refHour = Number.parseInt(refHourText, 10);
                const refMinute = Number.parseInt(refMinuteText, 10);
                if (Number.isNaN(refHour) || Number.isNaN(refMinute)) {
                    return;
                }

                const referenceAt = new Date(`${checkInDate}T${String(refHour).padStart(2, '0')}:${String(refMinute).padStart(2, '0')}:00`);
                if (checkedInAtInput) {
                    checkedInAtInput.value = now.toISOString();
                }
                if (effectivePlannedInput) {
                    effectivePlannedInput.value = referenceTime;
                }
                const diffMs = referenceAt.getTime() - now.getTime();
                const earlySeconds = Math.max(0, Math.floor(diffMs / 1000));
                const earlyHours = Math.ceil(earlySeconds / 3600);
                const surcharge = Math.max(0, Math.round(earlyHours * extraHourPrice));

                if (earlyHours > 0 && surcharge > 0) {
                    titleEl.textContent = `Khách đến sớm ${earlyHours} giờ so với giờ check-in dự kiến (${referenceTime}).`;
                    noteEl.textContent = `Phụ phí tạm tính theo giờ máy hiện tại: ${formatCurrency(surcharge)} VND.`;
                    return;
                }

                titleEl.textContent = 'Phụ phí check-in tạm tính: 0 VND.';
                noteEl.textContent = `Không phát sinh phụ phí đến sớm tại thời điểm hiện tại (mốc so sánh ${referenceTime}).`;
            };

            render();
            window.setInterval(render, 30000);
            if (referenceInput) {
                referenceInput.addEventListener('input', render);
                referenceInput.addEventListener('change', render);
            }
            if (form) {
                form.addEventListener('submit', () => {
                    if (checkedInAtInput) {
                        checkedInAtInput.value = new Date().toISOString();
                    }
                    if (effectivePlannedInput && referenceInput) {
                        effectivePlannedInput.value = referenceInput.value || defaultReferenceTime;
                    }
                });
            }
        })();
    </script>
</x-hotel-layout>
