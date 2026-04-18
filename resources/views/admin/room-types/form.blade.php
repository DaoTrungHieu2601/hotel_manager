<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Tên') }}</label>
    <input name="name" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900 placeholder:text-slate-400" value="{{ old('name', $type?->name) }}" required />
    @error('name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Mô tả') }}</label>
    <textarea name="description" rows="3" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900 placeholder:text-slate-400">{{ old('description', $type?->description) }}</textarea>
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Tiện nghi trong phòng') }} <span class="font-normal text-slate-500">({{ __('mỗi dòng một mục') }})</span></label>
    <textarea name="facilities" rows="4" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900 placeholder:text-slate-400" placeholder="{{ __('Ví dụ: Điều hòa 2 chiều') }}">{{ old('facilities', $type?->facilities) }}</textarea>
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Tiện ích') }} <span class="font-normal text-slate-500">({{ __('Wi‑Fi, bữa sáng… mỗi dòng một mục') }})</span></label>
    <textarea name="amenities" rows="4" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900 placeholder:text-slate-400">{{ old('amenities', $type?->amenities) }}</textarea>
</div>
<div class="grid gap-4 sm:grid-cols-3">
    <div>
        <label class="block text-xs font-semibold text-slate-700">{{ __('Giá mặc định') }}</label>
        <input type="number" step="1000" name="default_price" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" value="{{ old('default_price', $type?->default_price) }}" required />
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700">{{ __('Số giường') }}</label>
        <input type="number" name="beds" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" value="{{ old('beds', $type?->beds ?? 1) }}" required />
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700">{{ __('Sức chứa') }}</label>
        <input type="number" name="max_occupancy" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" value="{{ old('max_occupancy', $type?->max_occupancy ?? 2) }}" required />
    </div>
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Ảnh loại phòng') }}</label>
    <p class="mt-0.5 text-xs text-slate-500">{{ __('JPG, PNG, WebP, GIF… tối đa 20MB. Hiển thị trên trang tìm phòng và nơi giới thiệu loại phòng.') }}</p>
    @if ($type?->image_path)
        <div class="mt-2 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
            <img src="{{ asset('storage/'.$type->image_path) }}" alt="" class="h-40 w-full object-cover" />
        </div>
        <label class="mt-2 flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300 text-slate-900 accent-amber-900" @checked(old('remove_image')) />
            {{ __('Gỡ ảnh hiện tại') }}
        </label>
    @endif
    <div class="mt-2 flex flex-wrap items-center gap-3">
        <label class="inline-flex cursor-pointer focus-within:outline-none focus-within:ring-2 focus-within:ring-amber-600 focus-within:ring-offset-2 rounded-full">
            <span class="inline-flex items-center rounded-full bg-amber-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-amber-950/30 transition hover:bg-amber-800 active:bg-amber-950">{{ __('Chọn ảnh') }}</span>
            <input id="room-type-image" type="file" name="image" accept="image/*" class="sr-only" />
        </label>
        <span class="text-xs text-slate-600">{{ __('Nhấn để chọn tệp từ máy') }}</span>
    </div>
    @error('image')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
