<?php

namespace App\Http\Requests\Operator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDocumentTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-master-data');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('document_types', 'name')->ignore($this->route('document_type'))],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_required' => ['boolean'],
        ];
    }
    
    protected function prepareForValidation()
    {
        $this->merge([
            'is_required' => $this->has('is_required'),
        ]);
    }
}
