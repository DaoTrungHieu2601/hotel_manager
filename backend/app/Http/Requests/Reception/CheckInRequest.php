<?php

namespace App\Http\Requests\Reception;

use Illuminate\Foundation\Http\FormRequest;

class CheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id'         => ['required', 'exists:rooms,id'],
            'cccd'            => ['nullable', 'string', 'max:20'],
            'actual_check_in' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'Vui lòng chọn phòng.',
            'room_id.exists'   => 'Phòng không tồn tại.',
        ];
    }
}
