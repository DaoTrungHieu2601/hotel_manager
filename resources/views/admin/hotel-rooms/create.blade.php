<x-hotel-layout>
    <x-slot name="header">{{ __('Thêm phòng') }}</x-slot>
    @include('admin.hotel-rooms.form', ['room' => null, 'types' => $types])
</x-hotel-layout>
