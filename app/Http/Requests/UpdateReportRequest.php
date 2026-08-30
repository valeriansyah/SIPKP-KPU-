<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array<mixed>|string>
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
            'documents' => 'nullable|array',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $report = $this->route('report');
            if ($report) {
                $requiredDocs = $report->revisionItems()->where('is_resolved', false)->where('revision_type', 'document')->get();
                $uploadedDocs = $this->file('documents', []);
                
                foreach ($requiredDocs as $doc) {
                    if (!isset($uploadedDocs[$doc->document_type_id])) {
                        $validator->errors()->add('documents.'.$doc->document_type_id, 'Dokumen '.$doc->label.' wajib diunggah ulang.');
                    }
                }
            }
        });
    }
}
