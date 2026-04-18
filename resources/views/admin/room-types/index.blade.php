<x-hotel-layout>
    <x-slot name="header">{{ __('Loại phòng') }}</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-gray-600">{{ __('Giá và sức chứa mặc định theo loại phòng.') }}</p>
        <a href="{{ route('admin.room-types.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl bg-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2">
            {{ __('Thêm loại') }}
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
        @foreach($types as $t)
            <article class="group flex h-full flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm ring-1 ring-gray-100 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-purple-100">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="font-display text-xl font-bold leading-tight text-gray-900">
                        {{ $t->name }}
                    </h3>
                    <div class="flex shrink-0 items-center gap-1.5">
                        <a href="{{ route('admin.room-types.edit', $t) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-700 transition hover:border-purple-300 hover:bg-purple-50 hover:text-purple-800" title="{{ __('Sửa') }}" aria-label="{{ __('Sửa') }}">
                            <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current" aria-hidden="true">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zm17.71-10.04a1 1 0 0 0 0-1.41l-2.51-2.51a1 1 0 0 0-1.41 0l-1.84 1.84 3.75 3.75 2.01-1.67z"/>
                            </svg>
                        </a>
                        <form action="{{ route('admin.room-types.destroy', $t) }}" method="post" onsubmit="return confirm('{{ __('Bạn chắc chắn muốn xóa loại phòng này?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 bg-gray-50 text-gray-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-700" title="{{ __('Xóa') }}" aria-label="{{ __('Xóa') }}">
                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current" aria-hidden="true">
                                    <path d="M6 7h12l-1 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L6 7zm3-4h6l1 2h4v2H4V5h4l1-2z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-6 flex flex-1 flex-col justify-end border-t border-gray-100 pt-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ __('Giá mặc định') }}</p>
                    <p class="mt-1.5 text-2xl font-bold tabular-nums tracking-tight text-gray-900">
                        {{ number_format($t->default_price, 0, ',', '.') }}
                        <span class="text-lg font-semibold text-gray-600">₫</span>
                        <span class="text-base font-semibold text-gray-600">/ {{ __('đêm') }}</span>
                    </p>
                    <p class="mt-4">
                        <span class="inline-flex items-center rounded-lg bg-gray-100 px-3 py-1.5 text-sm font-semibold text-gray-800 ring-1 ring-gray-200/80">
                            {{ __('Tối đa :x người', ['x' => $t->max_occupancy]) }}
                        </span>
                    </p>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-6">{{ $types->links() }}</div>
</x-hotel-layout>
