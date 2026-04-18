@php($editing = $room !== null)
<form method="post" action="{{ $editing ? route('admin.hotel-rooms.update', $room) : route('admin.hotel-rooms.store') }}" class="max-w-xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 text-slate-900">
    @csrf
    @if($editing) @method('PUT') @endif
    <div>
        <label class="block text-xs font-semibold text-slate-700">{{ __('Loại phòng') }}</label>
        <select name="room_type_id" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" required>
            @foreach($types as $t)
                <option value="{{ $t->id }}" @selected(old('room_type_id', $room?->room_type_id) == $t->id)>{{ $t->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700">{{ __('Mã phòng') }}</label>
        <input name="code" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" value="{{ old('code', $room?->code) }}" required />
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700">{{ __('Tầng') }}</label>
        <input name="floor" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" value="{{ old('floor', $room?->floor) }}" />
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700">{{ __('Trạng thái') }}</label>
        <select name="status" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" required>
            @foreach(\App\Models\Room::statusLabels() as $k => $lbl)
                <option value="{{ $k }}" @selected(old('status', $room?->status ?? \App\Models\Room::STATUS_AVAILABLE) === $k)>{{ $lbl }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-700">{{ __('Ghi chú') }}</label>
        <textarea name="notes" rows="2" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900 placeholder:text-slate-400">{{ old('notes', $room?->notes) }}</textarea>
    </div>
    <button class="rounded-full bg-amber-900 px-6 py-2 text-white" type="submit">{{ $editing ? __('Cập nhật') : __('Lưu') }}</button>
</form>
