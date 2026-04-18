<x-hotel-layout><x-slot name="header">{{ __('Thêm loại phòng') }}</x-slot>
<form method="post" action="{{ route('admin.room-types.store') }}" enctype="multipart/form-data" class="max-w-xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 text-slate-900">@csrf
@include('admin.room-types.form', ['type' => null])
<button class="rounded-full bg-amber-900 px-6 py-2 text-white" type="submit">{{ __('Lưu') }}</button>
</form></x-hotel-layout>
