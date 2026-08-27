@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
        <div>
            <span class="text-xs font-bold tracking-wider text-primary uppercase mb-1 block">Portal Layanan Masyarakat</span>
            <h1 class="text-2xl font-bold text-text">Buat Laporan Kematian Pemilih</h1>
            <p class="text-sm text-muted mt-1">Lengkapi data berikut dengan benar untuk mengajukan laporan kematian pemilih ke KPU.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pelapor.laporan.index') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded hover:bg-gray-50 transition-colors text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                Kembali
            </a>
        </div>
    </div>

    <!-- PROGRESS INDICATOR -->
    <div class="hidden sm:block py-4">
        <ul class="relative flex w-full justify-between items-center">
            <li class="flex items-center text-primary font-medium w-full">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white font-bold shrink-0">1</div>
                <div class="ml-3 text-sm">Data Pelapor</div>
                <div class="flex-auto border-t-2 border-primary mx-4"></div>
            </li>
            <li class="flex items-center text-primary font-medium w-full">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white font-bold shrink-0">2</div>
                <div class="ml-3 text-sm">Data Almarhum</div>
                <div class="flex-auto border-t-2 border-primary mx-4"></div>
            </li>
            <li class="flex items-center text-primary font-medium w-full">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white font-bold shrink-0">3</div>
                <div class="ml-3 text-sm">Dokumen</div>
                <div class="flex-auto border-t-2 border-gray-200 mx-4"></div>
            </li>
            <li class="flex items-center text-gray-400 font-medium">
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-500 font-bold shrink-0">4</div>
                <div class="ml-3 text-sm">Review</div>
            </li>
        </ul>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Terdapat kesalahan pada isian form!</strong>
            <p class="text-sm mt-1">Mohon periksa kembali field yang ditandai dengan warna merah di bawah ini.</p>
        </div>
    @endif

    <form id="report-form" action="{{ route('pelapor.laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" novalidate>
        @csrf

        <!-- SECTION 1: Data Pelapor -->
        <x-ui.card class="border border-red-100 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-red-100 bg-red-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-bold text-xl shrink-0 shadow-inner">
                    1
                </div>
                <div>
                    <h2 class="text-lg font-bold text-red-900 uppercase tracking-wide">Data Pelapor</h2>
                    <p class="text-sm text-red-700/80 mt-1">Identitas pelapor diambil otomatis dari akun yang sedang Anda gunakan.</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/30">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="reporter_name">Nama</label>
                    <input type="text" id="reporter_name" value="{{ Auth::user()->full_name }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-md shadow-sm focus:ring-0 focus:border-gray-200" readonly aria-disabled="true">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="reporter_email">Email</label>
                    <input type="email" id="reporter_email" value="{{ Auth::user()->email }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-md shadow-sm focus:ring-0 focus:border-gray-200" readonly aria-disabled="true">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="reporter_phone">Nomor HP</label>
                    <input type="text" id="reporter_phone" value="{{ Auth::user()->phone_number }}" class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-md shadow-sm focus:ring-0 focus:border-gray-200" readonly aria-disabled="true">
                </div>
            </div>
        </x-ui.card>

        <!-- SECTION 2: Data Almarhum -->
        <x-ui.card class="border border-orange-100 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-orange-100 bg-orange-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold text-xl shrink-0 shadow-inner">
                    2
                </div>
                <div>
                    <h2 class="text-lg font-bold text-orange-900 uppercase tracking-wide">Data Almarhum</h2>
                    <p class="text-sm text-orange-700/80 mt-1">Masukkan identitas almarhum dengan teliti dan sesuai dengan dokumen resmi.</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">NIK Almarhum <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="text" name="nik" id="nik" value="{{ old('nik') }}" maxlength="16" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('nik') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" required placeholder="16 Digit NIK" aria-required="true" aria-invalid="{{ $errors->has('nik') ? 'true' : 'false' }}">
                    @error('nik')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="family_card_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kartu Keluarga <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="text" name="family_card_number" id="family_card_number" value="{{ old('family_card_number') }}" maxlength="16" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('family_card_number') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" required placeholder="16 Digit No KK" aria-required="true" aria-invalid="{{ $errors->has('family_card_number') ? 'true' : 'false' }}">
                    @error('family_card_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Almarhum <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('name') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" required aria-required="true" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500" aria-hidden="true">*</span></label>
                    <select name="gender" id="gender" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('gender') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" required aria-required="true" aria-invalid="{{ $errors->has('gender') ? 'true' : 'false' }}">
                        <option value="">Pilih Jenis Kelamin...</option>
                        <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="district_id" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota <span class="text-red-500" aria-hidden="true">*</span></label>
                    <select name="district_id" id="district_id" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('district_id') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" required aria-required="true" aria-invalid="{{ $errors->has('district_id') ? 'true' : 'false' }}">
                        <option value="">Pilih Kabupaten/Kota...</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>{{ $district->name }}</option>
                        @endforeach
                    </select>
                    @error('district_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="birth_place" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place') }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('birth_place') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" required aria-required="true" aria-invalid="{{ $errors->has('birth_place') ? 'true' : 'false' }}">
                    @error('birth_place')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('birth_date') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" required aria-required="true" aria-invalid="{{ $errors->has('birth_date') ? 'true' : 'false' }}">
                    @error('birth_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="death_place" class="block text-sm font-medium text-gray-700 mb-1">Tempat Meninggal</label>
                    <input type="text" name="death_place" id="death_place" value="{{ old('death_place') }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('death_place') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" placeholder="Opsional" aria-invalid="{{ $errors->has('death_place') ? 'true' : 'false' }}">
                    @error('death_place')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="death_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Meninggal <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="date" name="death_date" id="death_date" value="{{ old('death_date') }}" max="{{ now()->format('Y-m-d') }}" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('death_date') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" required aria-required="true" aria-invalid="{{ $errors->has('death_date') ? 'true' : 'false' }}">
                    @error('death_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat <span class="text-red-500" aria-hidden="true">*</span></label>
                    <textarea name="address" id="address" rows="3" class="w-full rounded-md shadow-sm focus:ring-primary {{ $errors->has('address') ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-primary' }}" required aria-required="true" aria-invalid="{{ $errors->has('address') ? 'true' : 'false' }}">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </x-ui.card>

        <!-- SECTION 3: Dokumen Pendukung -->
        <x-ui.card class="border border-blue-100 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-blue-100 bg-blue-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xl shrink-0 shadow-inner">
                    3
                </div>
                <div>
                    <h2 class="text-lg font-bold text-blue-900 uppercase tracking-wide">Dokumen Pendukung</h2>
                    <p class="text-sm text-blue-700/80 mt-1">Unggah dokumen yang dipersyaratkan. Pastikan file dapat dibaca dengan jelas.</p>
                </div>
            </div>
            <div class="p-6 space-y-8">
                @error('documents')
                    <div class="p-3 bg-red-50 text-red-700 text-sm rounded-md border border-red-200">
                        {{ $message }}
                    </div>
                @enderror
                
                <!-- Dokumen Wajib -->
                <div>
                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Dokumen Wajib
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($documentTypes->where('is_required', true) as $type)
                            @include('pelapor.laporan.partials.upload-card', ['type' => $type])
                        @endforeach
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Dokumen Opsional -->
                <div>
                    <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Dokumen Pendukung (Opsional)
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($documentTypes->where('is_required', false) as $type)
                            @include('pelapor.laporan.partials.upload-card', ['type' => $type])
                        @endforeach
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- SECTION 4: Konfirmasi & Submit -->
        <x-ui.card class="border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 md:p-8 border-b border-gray-200 bg-gray-50 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gray-200 text-gray-700 flex items-center justify-center font-bold text-xl shrink-0 shadow-inner">
                    4
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wide">Review & Kirim</h2>
                    <p class="text-sm text-gray-600 mt-1">Periksa kembali data Anda sebelum mengirimkan laporan.</p>
                </div>
            </div>
            <div class="p-6 bg-gray-50 flex flex-col gap-6">
                <div class="flex items-start bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center h-5 mt-0.5">
                        <input id="agreement" name="agreement" type="checkbox" required class="w-5 h-5 text-primary bg-white border-gray-300 rounded focus:ring-primary focus:ring-offset-white" {{ old('agreement') ? 'checked' : '' }} aria-required="true">
                    </div>
                    <label for="agreement" class="ml-3 text-sm text-gray-600 cursor-pointer select-none">
                        Dengan mencentang kotak ini, saya menyatakan bahwa data dan dokumen yang saya unggah adalah 
                        <strong class="font-semibold text-gray-900">benar dan sah secara hukum</strong>. 
                        Saya bersedia mempertanggungjawabkan keabsahan dokumen ini di kemudian hari.
                    </label>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-2">
                    <a href="{{ route('pelapor.laporan.index') }}" class="w-full sm:w-auto px-6 py-2.5 text-gray-600 font-medium hover:text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 text-center">
                        Kembali
                    </a>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <button type="button" class="w-full sm:w-auto px-6 py-2.5 bg-white text-gray-700 border border-gray-300 font-medium rounded hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 shadow-sm">
                            Simpan Draft
                        </button>
                        <button type="submit" id="submit-btn" class="w-full sm:w-auto px-8 py-2.5 bg-primary text-white font-medium rounded hover:bg-primary-dark transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Lanjutkan (Kirim Laporan)
                        </button>
                    </div>
                </div>
            </div>
        </x-ui.card>

    </form>
</div>

@push('scripts')
<script>
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    const ALLOWED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png'];

    function handleFileSelect(input, typeId) {
        const file = input.files[0];
        const card = document.getElementById(`upload-card-${typeId}`);
        const stateEmpty = document.getElementById(`state-empty-${typeId}`);
        const stateFilled = document.getElementById(`state-filled-${typeId}`);
        const stateError = document.getElementById(`state-error-${typeId}`);
        const errorText = stateError.querySelector('.error-text');
        
        // Reset Error State
        stateError.classList.add('hidden');
        card.classList.remove('border-red-500', 'bg-red-50/10');
        
        if (!file) {
            clearFileInput(typeId);
            return;
        }

        // Validate Extension
        const fileName = file.name.toLowerCase();
        const extension = fileName.split('.').pop();
        if (!ALLOWED_EXTENSIONS.includes(extension)) {
            showError(typeId, "Format file tidak didukung. Gunakan PDF, JPG, atau PNG.");
            input.value = '';
            return;
        }

        // Validate Size
        if (file.size > MAX_FILE_SIZE) {
            showError(typeId, "Ukuran file terlalu besar. Maksimal 5 MB.");
            input.value = '';
            return;
        }

        // Valid File Selected
        stateEmpty.classList.add('hidden');
        stateFilled.classList.remove('hidden');
        
        document.getElementById(`filename-${typeId}`).textContent = file.name;
        document.getElementById(`filesize-${typeId}`).textContent = formatBytes(file.size);
        
        // Card styling for success
        card.classList.add('border-primary', 'bg-primary/5');
    }

    function clearFileInput(typeId) {
        const input = document.getElementById(`doc_${typeId}`);
        const card = document.getElementById(`upload-card-${typeId}`);
        const stateEmpty = document.getElementById(`state-empty-${typeId}`);
        const stateFilled = document.getElementById(`state-filled-${typeId}`);
        const stateError = document.getElementById(`state-error-${typeId}`);
        
        input.value = '';
        stateEmpty.classList.remove('hidden');
        stateFilled.classList.add('hidden');
        stateError.classList.add('hidden');
        
        // Reset styling
        card.classList.remove('border-primary', 'bg-primary/5', 'border-red-500', 'bg-red-50/10');
    }

    function showError(typeId, message) {
        const card = document.getElementById(`upload-card-${typeId}`);
        const stateEmpty = document.getElementById(`state-empty-${typeId}`);
        const stateFilled = document.getElementById(`state-filled-${typeId}`);
        const stateError = document.getElementById(`state-error-${typeId}`);
        const errorText = stateError.querySelector('.error-text');
        
        stateEmpty.classList.remove('hidden');
        stateFilled.classList.add('hidden');
        stateError.classList.remove('hidden');
        errorText.textContent = message;
        
        card.classList.add('border-red-500', 'bg-red-50/10');
    }

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    // Optional: Drag and Drop support
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('[id^="upload-card-"]');
        
        cards.forEach(card => {
            const typeId = card.id.split('-').pop();
            const input = document.getElementById(`doc_${typeId}`);
            
            card.addEventListener('dragover', (e) => {
                e.preventDefault();
                card.classList.add('border-primary', 'bg-gray-50');
            });
            
            card.addEventListener('dragleave', (e) => {
                e.preventDefault();
                card.classList.remove('border-primary', 'bg-gray-50');
            });
            
            card.addEventListener('drop', (e) => {
                e.preventDefault();
                card.classList.remove('border-primary', 'bg-gray-50');
                
                if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    input.files = e.dataTransfer.files;
                    handleFileSelect(input, typeId);
                }
            });
        });
    });

    document.getElementById('report-form').addEventListener('submit', function(e) {
        let hasError = false;
        let firstErrorElement = null;

        // Reset previous errors
        document.querySelectorAll('.client-error').forEach(el => el.remove());
        document.querySelectorAll('.border-red-500').forEach(el => {
            if(el.tagName === 'INPUT' || el.tagName === 'SELECT' || el.tagName === 'TEXTAREA') {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            }
        });

        // 1. Validate Required Non-File Inputs
        const requiredInputs = this.querySelectorAll('input[required]:not([type="file"]), select[required], textarea[required]');
        requiredInputs.forEach(input => {
            let isInvalid = false;
            
            if (input.type === 'checkbox') {
                isInvalid = !input.checked;
            } else {
                isInvalid = !input.value.trim();
            }

            if (isInvalid) {
                hasError = true;
                
                // Add error styling
                input.classList.remove('border-gray-300');
                input.classList.add('border-red-500');
                
                // Create error message
                const errorMsg = document.createElement('p');
                errorMsg.className = 'mt-1 text-sm text-red-600 client-error';
                
                // Determine Field Name based on previous label
                let fieldName = 'Field ini';
                if (input.previousElementSibling && input.previousElementSibling.tagName === 'LABEL') {
                    fieldName = input.previousElementSibling.innerText.replace('*', '').trim();
                } else if (input.id === 'agreement') {
                    fieldName = 'Persetujuan';
                }
                
                errorMsg.innerText = fieldName + ' wajib diisi / dicentang.';
                
                // Append error message
                if (input.id === 'agreement') {
                    input.closest('.flex').parentNode.appendChild(errorMsg);
                } else {
                    input.parentNode.appendChild(errorMsg);
                }
                
                if (!firstErrorElement) firstErrorElement = input;
            }
        });

        // 2. Validate Required Documents
        const requiredDocs = this.querySelectorAll('input[type="file"][required]');
        requiredDocs.forEach(input => {
            if (input.files.length === 0) {
                hasError = true;
                const typeId = input.id.split('_')[1];
                showError(typeId, "Dokumen wajib dilampirkan.");
                
                const card = document.getElementById(`upload-card-${typeId}`);
                if (!firstErrorElement) firstErrorElement = card;
            }
        });

        if (hasError) {
            e.preventDefault(); // Stop form submission
            
            // Scroll to the first error
            if (firstErrorElement) {
                firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        } else {
            // Valid -> Disable button and show processing state
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.innerText = 'Memproses...';
        }
    });
</script>
@endpush

@if(!$errors->any() && empty(old()))
<!-- PRE-SUBMISSION CHECKLIST MODAL -->
<div id="preSubmissionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-labelledby="modal-title">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all flex flex-col">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Persiapkan Dokumen Pelaporan
            </h3>
        </div>
        <div class="px-6 py-5 space-y-4 flex-grow overflow-y-auto max-h-[60vh]">
            <p class="text-sm text-gray-600 leading-relaxed">
                Sebelum membuat laporan kematian pemilih, pastikan Anda telah mempersiapkan seluruh data dan dokumen yang diperlukan.
            </p>
            
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h4 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wide">Dokumen Wajib:</h4>
                <ul class="space-y-3">
                    @foreach($documentTypes->where('is_required', true) as $type)
                    <li class="flex items-start">
                        <div class="shrink-0 w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center mt-0.5 border border-green-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="ml-3 text-sm text-gray-800 font-medium">{{ $type->name }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="flex items-start gap-2 text-xs text-blue-700 bg-blue-50 p-3 rounded-lg border border-blue-100">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p>Pastikan dokumen dapat dibaca dengan jelas dan menggunakan format file yang diperbolehkan oleh sistem.</p>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row-reverse gap-3 bg-gray-50/80">
            <button type="button" id="btn-close-modal" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-primary text-white font-bold rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors shadow-sm">
                Saya Sudah Menyiapkan Dokumen
            </button>
            <a href="{{ route('pelapor.dashboard') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-colors shadow-sm">
                Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('preSubmissionModal');
        const closeBtn = document.getElementById('btn-close-modal');
        
        if (modal) {
            // Accessibility & scroll lock
            document.body.style.overflow = 'hidden';
            setTimeout(() => { if(closeBtn) closeBtn.focus(); }, 100);
            
            closeBtn.addEventListener('click', function() {
                modal.style.opacity = '0';
                modal.style.pointerEvents = 'none';
                setTimeout(() => {
                    modal.remove();
                    document.body.style.overflow = '';
                }, 200);
            });
        }
    });
</script>
@endif

@endsection
