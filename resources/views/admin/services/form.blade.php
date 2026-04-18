<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Tên') }}</label>
    <input name="name" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900 placeholder:text-slate-400" value="{{ old('name', $service?->name) }}" required />
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Giá') }}</label>
    <input type="number" step="1000" name="price" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" value="{{ old('price', $service?->price) }}" required />
</div>
<div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300 text-slate-900 accent-amber-900" @checked(old('is_active', $service?->is_active ?? true)) />
    <span class="text-sm text-slate-700">{{ __('Đang kinh doanh') }}</span>
</div>
