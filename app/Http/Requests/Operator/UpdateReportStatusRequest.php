<?php

namespace App\Http\Requests\Operator;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-master-data');
    }

    public function rules(): array
    {
        return [
            // Only description is editable to protect canonical workflow identifiers
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
