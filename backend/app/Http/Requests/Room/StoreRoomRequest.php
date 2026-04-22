<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roomId = $this->route('room')?->id;

        return [
            'room_type_id' => ['required', 'exists:room_types,id'],
            'code'         => ['required', 'string', 'max:20', Rule::unique('rooms', 'code')->ignore($roomId)],
            'floor'        => ['required', 'string', 'max:10'],
            'status'       => ['sometimes', 'in:available,occupied,booked,cleaning,maintenance'],
            'notes'        => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'Mã phòng đã tồn tại.',
        ];
    }
}
