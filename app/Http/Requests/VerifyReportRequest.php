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
        ];
    }
}
