<?php

namespace App\Http\Requests\Staff;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $staffId = $this->route('staff')?->id;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name'        => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'email'       => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                Rule::unique('users', 'email')->ignore($staffId),
            ],
            'password'    => [$isUpdate ? 'nullable' : 'required', Password::defaults()],
            'role'        => [$isUpdate ? 'sometimes' : 'required', Rule::in(User::staffRoleSlugs())],
            'phone'       => ['nullable', 'string', 'max:20'],
            'cccd'        => ['nullable', 'string', 'max:20'],
            'address'     => ['nullable', 'string'],
            'position_id' => ['nullable', 'exists:positions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email đã được sử dụng.',
            'role.in'      => 'Vai trò không hợp lệ.',
        ];
    }
}
