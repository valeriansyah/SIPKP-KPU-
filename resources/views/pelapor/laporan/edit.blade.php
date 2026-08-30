@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
        <div>
            <span class="text-xs font-bold tracking-wider text-primary uppercase mb-1 block">Portal Layanan Masyarakat</span>
            <h1 class="text-2xl font-bold text-text">Perbaiki Laporan Kematian</h1>
            <p class="text-sm text-muted mt-1">Perbarui data laporan No: {{ $report->report_number }} sesuai arahan verifikator.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pelapor.laporan.show', $report->id) }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded hover:bg-gray-50 transition-colors text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                Batal
            </a>
        </div>
    </div>

    @if(isset($revisionItems) && $revisionItems->isNotEmpty())
        <div class="bg-orange-50 border-l-4 border-orange-500 rounded-r-lg p-5 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-bold text-orange-900">Perbaikan Diperlukan</h3>
                    
                    @php $lastVerification = $report->reportVerifications->sortByDesc('created_at')->first(); @endphp
                    @if($lastVerification && $lastVerification->notes)
                    <div class="mt-2 text-sm text-orange-800 bg-white/60 p-3 rounded border border-orange-200">
                        <span class="font-semibold block mb-1">Catatan Verifikator:</span>
                        {{ $lastVerification->notes }}
                    </div>
                    @endif

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($revisionFields && count($revisionFields) > 0)
                        <div>
                            <h4 class="font-bold text-orange-900 text-sm mb-2">Data yang harus diperbaiki:</h4>
                            <ul class="list-disc pl-5 text-sm text-orange-800 space-y-1">
                                @foreach($revisionItems->where('revision_type', 'data') as $item)
                                    <li>{{ $item->label }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if($revisionDocuments && $revisionDocuments->count() > 0)
                        <div>
                            <h4 class="font-bold text-orange-900 text-sm mb-2">Dokumen yang harus diunggah ulang:</h4>
                            <ul class="list-disc pl-5 text-sm text-orange-800 space-y-1">
                                @foreach($revisionDocuments as $item)
                                    <li>{{ $item->label }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Terdapat kesalahan pada isian form!</strong>
            <p class="text-sm mt-1">Mohon periksa kembali field yang ditandai dengan warna merah di bawah ini.</p>
        </div>
    @endif

    <form action="{{ route('pelapor.laporan.update', $report->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8" novalidate onsubmit="document.getElementById('submit-btn').disabled = true; document.getElementById('submit-btn').innerText = 'Memproses...';">
        @csrf
        @method('PUT')

        <!-- SECTION: Data Almarhum -->
        <x-ui.card class="border border-orange-100 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-orange-100 bg-orange-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-xl shrink-0 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-orange-900 uppercase tracking-wide">Data Almarhum</h2>
                    <p class="text-sm text-orange-700/80 mt-1">Perbaiki identitas almarhum dengan teliti dan sesuai dengan dokumen resmi.</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK Almarhum <span class="text-red-500" aria-hidden="true">*</span> @if(isset($revisionFields) && in_array('nik', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <input type="text" name="nik" id="nik" value="{{ old('nik', $report->deceased->nik) }}" maxlength="16" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('nik') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('nik', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" required placeholder="16 Digit NIK" aria-required="true" aria-invalid="{{ $errors->has('nik') ? 'true' : 'false' }}">
                    @error('nik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="family_card_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kartu Keluarga <span class="text-red-500" aria-hidden="true">*</span> @if(isset($revisionFields) && in_array('family_card_number', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <input type="text" name="family_card_number" id="family_card_number" value="{{ old('family_card_number', $report->deceased->family_card_number) }}" maxlength="16" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('family_card_number') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('family_card_number', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" required placeholder="16 Digit No KK" aria-required="true" aria-invalid="{{ $errors->has('family_card_number') ? 'true' : 'false' }}">
                    @error('family_card_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Almarhum <span class="text-red-500" aria-hidden="true">*</span> @if(isset($revisionFields) && in_array('name', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $report->deceased->name) }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('name') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('name', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" required aria-required="true" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500" aria-hidden="true">*</span> @if(isset($revisionFields) && in_array('gender', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <select name="gender" id="gender" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('gender') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('gender', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" required aria-required="true" aria-invalid="{{ $errors->has('gender') ? 'true' : 'false' }}">
                        <option value="">Pilih Jenis Kelamin...</option>
                        <option value="Laki-laki" {{ old('gender', $report->deceased->gender) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender', $report->deceased->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota <span class="text-red-500" aria-hidden="true">*</span> @if(isset($revisionFields) && in_array('district_id', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <select name="district_id" id="district_id" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('district_id') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('district_id', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" required aria-required="true" aria-invalid="{{ $errors->has('district_id') ? 'true' : 'false' }}">
                        <option value="">Pilih Kabupaten/Kota...</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ old('district_id', $report->deceased->district_id) == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                        @endforeach
                    </select>
                    @error('district_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span class="text-red-500" aria-hidden="true">*</span> @if(isset($revisionFields) && in_array('birth_place', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $report->deceased->birth_place) }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('birth_place') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('birth_place', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" required aria-required="true" aria-invalid="{{ $errors->has('birth_place') ? 'true' : 'false' }}">
                    @error('birth_place')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500" aria-hidden="true">*</span> @if(isset($revisionFields) && in_array('birth_date', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $report->deceased->birth_date ? $report->deceased->birth_date->format('Y-m-d') : '') }}" max="{{ date('Y-m-d') }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('birth_date') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('birth_date', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" required aria-required="true" aria-invalid="{{ $errors->has('birth_date') ? 'true' : 'false' }}">
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="death_place" class="block text-sm font-medium text-gray-700 mb-1">Tempat Meninggal <span class="text-gray-400 font-normal">(Opsional)</span> @if(isset($revisionFields) && in_array('death_place', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <input type="text" name="death_place" id="death_place" value="{{ old('death_place', $report->deceased->death_place) }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('death_place') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('death_place', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" aria-invalid="{{ $errors->has('death_place') ? 'true' : 'false' }}">
                    @error('death_place')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="death_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Meninggal <span class="text-red-500" aria-hidden="true">*</span> @if(isset($revisionFields) && in_array('death_date', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <input type="date" name="death_date" id="death_date" value="{{ old('death_date', $report->deceased->death_date ? $report->deceased->death_date->format('Y-m-d') : '') }}" max="{{ date('Y-m-d') }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('death_date') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('death_date', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" required aria-required="true" aria-invalid="{{ $errors->has('death_date') ? 'true' : 'false' }}">
                    @error('death_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap Almarhum <span class="text-red-500" aria-hidden="true">*</span> @if(isset($revisionFields) && in_array('address', $revisionFields)) <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diperbaiki</span> @endif</label>
                    <textarea name="address" id="address" rows="3" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('address') ? 'border-red-500 focus:border-red-500' : ((isset($revisionFields) && in_array('address', $revisionFields)) ? 'border-orange-400 focus:border-orange-500 ring-1 ring-orange-200' : 'border-gray-300 focus:border-primary') }}" required aria-required="true" aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}">{{ old('address', $report->deceased->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-ui.card>

        @if(isset($revisionDocuments) && $revisionDocuments->count() > 0)
        <!-- SECTION: Dokumen yang Perlu Diperbaiki -->
        <x-ui.card class="border border-orange-100 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-orange-100 bg-orange-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-xl shrink-0 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-orange-900 uppercase tracking-wide">Unggah Ulang Dokumen</h2>
                    <p class="text-sm text-orange-700/80 mt-1">Silakan unggah file baru untuk menggantikan dokumen yang ditandai oleh verifikator.</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($revisionDocuments as $revDoc)
                    <div>
                        <label for="document_{{ $revDoc->document_type_id }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $revDoc->label }} <span class="text-red-500" aria-hidden="true">*</span> <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">Perlu diunggah ulang</span></label>
                        <input type="file" name="documents[{{ $revDoc->document_type_id }}]" id="document_{{ $revDoc->document_type_id }}" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer border border-orange-400 focus:border-orange-500 ring-1 ring-orange-200 rounded-md p-1 bg-white" required>
                        <p class="mt-1 text-xs text-gray-500">Maksimal ukuran 2MB. Format: PDF, JPG, PNG.</p>
                        @error('documents.'.$revDoc->document_type_id)
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </x-ui.card>
        @endif

        <!-- SECTION: Konfirmasi & Submit -->
        <x-ui.card class="border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 bg-gray-50 flex flex-col gap-6">
                <div class="flex items-start bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center h-5 mt-0.5">
                        <input id="agreement" name="agreement" type="checkbox" required class="w-5 h-5 text-primary bg-white border-gray-300 rounded focus:ring-primary focus:ring-offset-white" {{ old('agreement') ? 'checked' : '' }} aria-required="true">
                    </div>
                    <label for="agreement" class="ml-3 text-sm text-gray-600 cursor-pointer select-none">
                        Dengan mencentang kotak ini, saya menyatakan bahwa perbaikan data ini adalah 
                        <strong class="font-semibold text-gray-900">benar dan sah secara hukum</strong>.
                    </label>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
                    <a href="{{ route('pelapor.laporan.show', $report->id) }}" class="w-full sm:w-auto px-6 py-2.5 text-gray-600 font-medium hover:text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 text-center">
                        Batal
                    </a>
                    <button type="submit" id="submit-btn" class="w-full sm:w-auto px-8 py-2.5 bg-primary text-white font-medium rounded hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        Simpan Perbaikan
                    </button>
                </div>
            </div>
        </x-ui.card>
    </form>
</div>
@endsection
