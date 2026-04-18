<x-hotel-layout><x-slot name="header">{{ __('Sửa dịch vụ') }}</x-slot>
<form method="post" action="{{ route('admin.services.update', $service) }}" class="max-w-xl space-y-4 rounded-2xl border border-slate-200 bg-white p-6 text-slate-900">@csrf @method('PUT')
@include('admin.services.form', ['service' => $service])
<button class="rounded-full bg-amber-900 px-6 py-2 text-white" type="submit">{{ __('Cập nhật') }}</button>
</form></x-hotel-layout>
