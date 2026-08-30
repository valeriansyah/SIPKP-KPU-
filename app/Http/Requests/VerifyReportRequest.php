<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'decision' => 'required|string|in:diproses,perlu_perbaikan,disetujui,ditolak',
            'notes' => 'nullable|string',
            'revision_fields' => 'nullable|array',
            'revision_fields.*' => 'string|in:nik,family_card_number,name,gender,birth_place,birth_date,death_place,death_date,address,district_id',
            'revision_documents' => 'nullable|array',
            'revision_documents.*' => 'integer|exists:document_types,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $decision = $this->input('decision');
            if ($decision === 'perlu_perbaikan') {
                $fields = $this->input('revision_fields', []);
                $docs = $this->input('revision_documents', []);
                if (empty($fields) && empty($docs)) {
                    $validator->errors()->add('decision', 'Untuk status Perlu Perbaikan, minimal satu data atau satu dokumen harus dipilih untuk diperbaiki.');
                }
            }
        });
    }
}
