<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by Policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'district_id' => 'sometimes|required|exists:districts,id',
            'nik' => 'sometimes|required|string|size:16',
            'family_card_number' => 'sometimes|required|string|size:16',
            'name' => 'sometimes|required|string|max:255',
            'gender' => 'sometimes|required|in:Laki-laki,Perempuan',
            'birth_place' => 'sometimes|required|string|max:255',
            'birth_date' => 'sometimes|required|date',
            'address' => 'sometimes|required|string',
            'death_place' => 'nullable|string|max:255',
            'death_date' => 'sometimes|required|date',
        ];
    }
}
