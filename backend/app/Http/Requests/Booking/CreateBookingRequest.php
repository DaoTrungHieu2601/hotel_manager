<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_type_id'              => ['required', 'exists:room_types,id'],
            'check_in'                  => ['required', 'date', 'after_or_equal:today'],
            'check_out'                 => ['required', 'date', 'after:check_in'],
            'guests'                    => ['required', 'integer', 'min:1'],
            'guest_notes'               => ['nullable', 'string', 'max:1000'],
            'guest_planned_check_in'    => ['nullable', 'string', 'max:10'],
            'guest_planned_check_out'   => ['nullable', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_type_id.required' => 'Vui lòng chọn loại phòng.',
            'room_type_id.exists'   => 'Loại phòng không tồn tại.',
            'check_in.after_or_equal' => 'Ngày check-in phải từ hôm nay trở đi.',
            'check_out.after'       => 'Ngày check-out phải sau ngày check-in.',
            'guests.min'            => 'Số khách phải ít nhất là 1.',
        ];
    }
}
