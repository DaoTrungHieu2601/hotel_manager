<?php

namespace App\Http\Requests\RoomType;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name'          => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'facilities'    => ['nullable', 'string'],
            'amenities'     => ['nullable', 'string'],
            'default_price' => [$isUpdate ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'beds'          => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:1'],
            'max_occupancy' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:1'],
        ];
    }
}
