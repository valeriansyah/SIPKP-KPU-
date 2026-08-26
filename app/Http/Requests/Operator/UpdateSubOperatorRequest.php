<?php

namespace App\Http\Requests\Operator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubOperatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-sub-operator');
    }

    public function rules(): array
    {
        // the route parameter name is sub_operator due to resourceful routing
        $userId = $this->route('sub_operator')->id ?? null;
        
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
            'district_id' => ['required', 'exists:districts,id'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.max' => 'Nomor telepon tidak boleh lebih dari 20 karakter.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }
}
