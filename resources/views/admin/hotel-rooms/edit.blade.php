<x-hotel-layout>
    <x-slot name="header">{{ __('Sửa phòng') }}</x-slot>
    @include('admin.hotel-rooms.form', ['room' => $room, 'types' => $types])
</x-hotel-layout>
