<?php

namespace App\Http\Requests;

use App\Models\Deceased;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by Controller/Gate
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'district_id' => 'required|exists:districts,id',
            'nik' => [
                'required',
                'string',
                'size:16',
                function ($attribute, $value, $fail) {
                    $exists = Deceased::where('nik', $value)
                        ->whereHas('report', function ($query) {
                            $query->whereHas('reportStatus', function ($q) {
                                $q->whereIn('status_name', ['Pending', 'Disetujui']);
                            });
                        })->exists();

                    if ($exists) {
                        $fail('Laporan untuk NIK ini sedang diproses atau sudah disetujui.');
                    }
                },
            ],
            'family_card_number' => 'required|string|size:16',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'address' => 'required|string',
            'death_place' => 'nullable|string|max:255',
            'death_date' => 'required|date|after_or_equal:birth_date',

            // Document Validations
            'documents' => 'required|array',
            'documents.1' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Surat Keterangan Kematian
            'documents.2' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // KTP Almarhum
            'documents.3' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Kartu Keluarga
            'documents.6' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // KTP Pelapor

            // Optional Documents
            'documents.4' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Surat Pengantar
            'documents.5' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Visum
            'documents.7' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Akta Kelahiran
            'documents.8' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Foto Almarhum
        ];
    }

    public function messages(): array
    {
        return [
            'district_id.required' => 'Kabupaten/Kota wajib dipilih.',
            'district_id.exists' => 'Kabupaten/Kota tidak valid.',
            'nik.required' => 'NIK Almarhum wajib diisi.',
            'nik.size' => 'NIK harus berjumlah 16 karakter.',
            'family_card_number.required' => 'Nomor Kartu Keluarga wajib diisi.',
            'family_card_number.size' => 'Nomor Kartu Keluarga harus berjumlah 16 karakter.',
            'name.required' => 'Nama lengkap almarhum wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'birth_place.required' => 'Tempat lahir wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'death_date.required' => 'Tanggal meninggal wajib diisi.',
            'death_date.after_or_equal' => 'Tanggal meninggal tidak boleh mendahului tanggal lahir.',

            'documents.1.required' => 'Surat Keterangan Kematian wajib diunggah.',
            'documents.2.required' => 'KTP Almarhum wajib diunggah.',
            'documents.3.required' => 'Kartu Keluarga wajib diunggah.',
            'documents.6.required' => 'KTP Pelapor wajib diunggah.',
            'documents.*.mimes' => 'Format file harus PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran file maksimal 5MB.',
        ];
    }
}
