<x-hotel-layout>
    <x-slot name="header">{{ __('Sơ đồ phòng') }}</x-slot>

    <div class="select-none" style="-webkit-user-select:none;-moz-user-select:none;user-select:none;"
         x-data="{ activeFloor: 'all' }">

        @if (session('status'))
            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-900">{{ session('error') }}</div>
        @endif

        {{-- Chú thích màu sắc --}}
        <div class="mb-5 flex flex-wrap gap-x-6 gap-y-2 text-xs font-medium text-gray-700">
            <span class="inline-flex items-center gap-2"><span class="inline-block h-3 w-3 rounded border-2 border-emerald-500 bg-emerald-100"></span> {{ __('Trống') }}</span>
            <span class="inline-flex items-center gap-2"><span class="inline-block h-3 w-3 rounded border-2 border-rose-500 bg-rose-100"></span> {{ __('Đang có khách') }}</span>
            <span class="inline-flex items-center gap-2"><span class="inline-block h-3 w-3 rounded border-2 border-amber-500 bg-amber-100"></span> {{ __('Đang dọn dẹp') }}</span>
            <span class="inline-flex items-center gap-2"><span class="inline-block h-3 w-3 rounded border-2 border-sky-500 bg-sky-100"></span> {{ __('Đã đặt trước') }}</span>
        </div>

        {{-- Thống kê --}}
        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Chờ duyệt') }}</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">{{ $pendingCount }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Check-in hôm nay') }}</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $todayIn }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Check-out hôm nay') }}</p>
                <p class="mt-1 text-2xl font-bold text-sky-600">{{ $todayOut }}</p>
            </div>
        </div>

        {{-- Nút chọn tầng --}}
        <div class="mb-6 flex flex-wrap gap-2">
            <button type="button"
                @click="activeFloor = 'all'"
                :class="activeFloor === 'all'
                    ? 'bg-purple-600 text-white ring-2 ring-purple-300 shadow-sm'
                    : 'border border-gray-200 bg-gray-100 text-gray-800 hover:bg-gray-200'"
                class="rounded-xl px-4 py-2 text-sm font-semibold transition">
                Tất cả
            </button>
            @foreach($floors as $floor)
                <button type="button"
                    @click="activeFloor = {{ $floor }}"
                    :class="activeFloor === {{ $floor }}
                        ? 'bg-purple-600 text-white ring-2 ring-purple-300 shadow-sm'
                        : 'border border-gray-200 bg-gray-100 text-gray-800 hover:bg-gray-200'"
                    class="rounded-xl px-4 py-2 text-sm font-semibold transition">
                    Tầng {{ $floor }}
                </button>
            @endforeach
        </div>

        {{-- Phòng nhóm theo tầng --}}
        @foreach($roomsByFloor as $floor => $floorRooms)
            <div x-show="activeFloor === 'all' || activeFloor === {{ $floor }}"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mb-8">

                {{-- Tiêu đề tầng --}}
                <div class="mb-3 flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 ring-1 ring-purple-200">
                        <span class="text-xs font-bold text-purple-800">{{ $floor }}</span>
                    </div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900">
                        Tầng {{ $floor }}
                        <span class="ml-2 text-xs font-normal normal-case text-gray-600">({{ $floorRooms->count() }} phòng)</span>
                    </h2>
                    <div class="flex-1 border-t border-gray-200"></div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                    @foreach($floorRooms as $room)
                    @php($cardStyles = [
                        \App\Models\Room::STATUS_AVAILABLE    => ['card' => 'border-emerald-200 bg-emerald-50', 'badge' => 'text-emerald-800 ring-1 ring-emerald-200/80 bg-emerald-100/80'],
                        \App\Models\Room::STATUS_OCCUPIED     => ['card' => 'border-rose-200 bg-rose-50', 'badge' => 'text-rose-800 ring-1 ring-rose-200/80 bg-rose-100/80'],
                        \App\Models\Room::STATUS_BOOKED       => ['card' => 'border-sky-200 bg-sky-50', 'badge' => 'text-sky-800 ring-1 ring-sky-200/80 bg-sky-100/80'],
                        \App\Models\Room::STATUS_CLEANING     => ['card' => 'border-amber-200 bg-amber-50', 'badge' => 'text-amber-800 ring-1 ring-amber-200/80 bg-amber-100/80'],
                        \App\Models\Room::STATUS_MAINTENANCE  => ['card' => 'border-slate-300 bg-slate-100', 'badge' => 'text-slate-800 ring-1 ring-slate-300 bg-slate-200/80'],
                    ])
                    @php($st = $cardStyles[$room->status] ?? ['card' => 'border-gray-200 bg-white', 'badge' => 'text-gray-800 ring-1 ring-gray-200 bg-gray-100'])
                    @php($activeBooking = $activeByRoom[$room->id] ?? null)
                        <div class="rounded-2xl border-2 p-4 shadow-sm {{ $st['card'] }}">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="font-display text-lg font-bold text-gray-900">{{ $room->code }}</p>
                                    <p class="text-xs text-gray-600">{{ $room->roomType->name }}</p>
                                </div>
                                <span class="shrink-0 rounded px-1.5 py-0.5 text-[10px] font-bold uppercase {{ $st['badge'] }}">{{ \App\Models\Room::statusLabels()[$room->status] ?? $room->status }}</span>
                            </div>
                            @if($activeBooking)
                                <a href="{{ route('reception.reservations.show', $activeBooking) }}" class="mt-3 block text-center text-xs font-semibold text-amber-800 underline decoration-amber-300 underline-offset-2 hover:text-amber-900">{{ __('Chi tiết đơn / dịch vụ') }}</a>
                            @endif
                            @if(in_array($room->status, [\App\Models\Room::STATUS_AVAILABLE, \App\Models\Room::STATUS_CLEANING, \App\Models\Room::STATUS_MAINTENANCE], true))
                                <form method="post" action="{{ route('reception.rooms.status', $room) }}" class="mt-3 text-xs">
                                    @csrf
                                    @method('PATCH')
                                    <label class="sr-only">{{ __('Trạng thái') }}</label>
                                    <select name="status" class="select-auto w-full rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-xs text-gray-900 shadow-sm focus:border-purple-400 focus:outline-none focus:ring-1 focus:ring-purple-300" onchange="this.form.submit()">
                                        <option value="{{ \App\Models\Room::STATUS_AVAILABLE }}"    @selected($room->status === \App\Models\Room::STATUS_AVAILABLE)>{{ __('Trống') }}</option>
                                        <option value="{{ \App\Models\Room::STATUS_CLEANING }}"     @selected($room->status === \App\Models\Room::STATUS_CLEANING)>{{ __('Dọn dẹp') }}</option>
                                        <option value="{{ \App\Models\Room::STATUS_MAINTENANCE }}"  @selected($room->status === \App\Models\Room::STATUS_MAINTENANCE)>{{ __('Bảo trì') }}</option>
                                    </select>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-hotel-layout>
