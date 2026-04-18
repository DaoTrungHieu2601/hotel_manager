<x-hotel-layout><x-slot name="header">{{ __('Sửa loại phòng') }}</x-slot>
<form method="post" action="{{ route('admin.room-types.update', $type) }}" enctype="multipart/form-data" class="max-w-xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 text-slate-900">@csrf @method('PUT')
@include('admin.room-types.form', ['type' => $type])
<button class="rounded-full bg-amber-900 px-6 py-2 text-white" type="submit">{{ __('Cập nhật') }}</button>
</form></x-hotel-layout>
