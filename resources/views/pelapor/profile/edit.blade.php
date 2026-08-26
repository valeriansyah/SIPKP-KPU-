@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="max-w-4xl mx-auto py-6 lg:py-8 space-y-6">
        
        <!-- Header Page -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola informasi akun pelapor Anda.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl shadow-sm flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif
        
        <form action="{{ route('pelapor.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="profile-form">
            @csrf
            @method('PUT')
            
            <!-- Hidden input for photo removal -->
            <input type="hidden" name="remove_photo" id="remove_photo_input" value="0">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Profile Summary Card (Left Column) -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="h-28 bg-gradient-to-r from-red-600 to-red-800 relative overflow-hidden">
                            <!-- Subtle Pattern Overlay -->
                            <div class="absolute inset-0 opacity-10">
                                <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="red-pattern" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M0 40L40 0H20L0 20M40 40V20L20 40" fill="currentColor" fill-opacity="0.4"/></pattern></defs><rect width="100%" height="100%" fill="url(#red-pattern)"/></svg>
                            </div>
                        </div>
                        <div class="px-6 pb-6 relative">
                            <!-- Avatar Container -->
                            <div class="relative flex justify-center -mt-12 mb-4">
                                <div class="w-24 h-24 rounded-full bg-white p-1 shadow-md border border-gray-100 relative group cursor-pointer" onclick="document.getElementById('profile_picture').click()">
                                    <!-- Current Image or Initial using Component -->
                                    <x-ui.avatar :user="$user" size="xl" id="profile-avatar" class="w-full h-full !border-0 !shadow-none" />
                                    
                                    <!-- Hover Overlay -->
                                    <div class="absolute inset-1 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    
                                    <!-- Role Badge (Overlay) -->
                                    <span class="absolute bottom-0 right-0 w-6 h-6 rounded-full border-2 border-white bg-red-600 flex items-center justify-center" title="Pelapor">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </span>
                                </div>
                                <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">
                            </div>
                            
                            <!-- User Info -->
                            <div class="text-center">
                                <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $user->full_name }}</h3>
                                <div class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-red-50 text-red-700 text-[11px] font-semibold tracking-wide uppercase mt-2">
                                    Pelapor
                                </div>
                                <p class="text-sm text-gray-500 mt-3 truncate px-2" title="{{ $user->email }}">{{ $user->email }}</p>
                                <div class="mt-4 flex items-center justify-center gap-1.5 text-sm">
                                    <span class="relative flex h-2.5 w-2.5">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                    </span>
                                    <span class="text-gray-700 font-medium">Status Akun: <span class="text-green-600 font-bold">Aktif</span></span>
                                </div>
                            </div>
                            
                            <!-- Photo Actions -->
                            <div class="mt-6 flex flex-col gap-2">
                                <button type="button" onclick="document.getElementById('profile_picture').click()" class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    Ganti Foto
                                </button>
                                <button type="button" id="remove-btn" onclick="removePhoto()" class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-white border border-transparent rounded-lg hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors {{ $user->profile_photo_url ? '' : 'hidden' }}">
                                    Hapus Foto
                                </button>
                            </div>
                            @error('profile_picture')
                                <p class="text-red-500 text-xs text-center mt-3">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Card (Right Column) -->
                <div class="md:col-span-2 space-y-6">
                    
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        
                        <!-- Account Info Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Informasi Akun</h3>
                            
                            <!-- Readonly Email -->
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Utama (Google)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                                    </div>
                                    <input type="email" value="{{ $user->email }}" disabled class="bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg block w-full pl-10 p-2.5 cursor-not-allowed">
                                </div>
                                <p class="text-[11px] text-gray-500 mt-1.5 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Email tertaut dengan Google OAuth dan tidak dapat diubah.
                                </p>
                            </div>
                            
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-1" for="full_name">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input id="full_name" type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 transition-colors @error('full_name') border-red-500 @else border-gray-300 @enderror" placeholder="Masukkan nama lengkap Anda">
                                @error('full_name')
                                    <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Contact Section -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Kontak</h3>
                            
                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-1" for="district_id">Kabupaten/Kota <span class="text-red-500">*</span></label>
                                <select id="district_id" name="district_id" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 transition-colors @error('district_id') border-red-500 @else border-gray-300 @enderror">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}" {{ old('district_id', $user->district_id) == $district->id ? 'selected' : '' }}>
                                            {{ $district->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-gray-500 mt-1.5 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Kontak Sub Operator akan disesuaikan dengan Kabupaten/Kota yang Anda pilih.
                                </p>
                                @error('district_id')
                                    <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-5">
                                <label class="block text-sm font-semibold text-gray-700 mb-1" for="phone_number">Nomor HP/WhatsApp <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number == '-' ? '' : $user->phone_number) }}" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full pl-10 p-2.5 transition-colors @error('phone_number') border-red-500 @else border-gray-300 @enderror" placeholder="Contoh: 08123456789">
                                </div>
                                @error('phone_number')
                                    <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Security Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 border-b border-gray-100 pb-2">Keamanan</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1" for="password">Kata Sandi Baru</label>
                                    <input id="password" type="password" name="password" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 transition-colors @error('password') border-red-500 @else border-gray-300 @enderror" placeholder="Biarkan kosong jika tidak diubah">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1" for="password_confirmation">Konfirmasi Kata Sandi</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation" class="bg-white border text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 transition-colors border-gray-300" placeholder="Ulangi kata sandi baru">
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-2">
                                Kosongkan kolom ini jika Anda tidak ingin mengubah password. Pengaturan password berguna untuk alternatif login tanpa Google OAuth.
                            </p>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3 mt-8">
                            <a href="{{ route('pelapor.dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-900 transition-colors focus:ring-4 focus:outline-none focus:ring-gray-200">
                                Batal
                            </a>
                            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-700 rounded-lg hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 transition-colors shadow-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Scripts for Image Preview -->
    <script>
        function previewImage(input) {
            const preview = document.querySelector('#profile-avatar .avatar-image');
            const initial = document.querySelector('#profile-avatar .avatar-initial');
            const removeBtn = document.getElementById('remove-btn');
            const removeFlag = document.getElementById('remove_photo_input');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if(initial) initial.classList.add('hidden');
                    removeBtn.classList.remove('hidden');
                    removeFlag.value = "0"; // Reset remove flag if they choose a new file
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removePhoto() {
            const preview = document.querySelector('#profile-avatar .avatar-image');
            const initial = document.querySelector('#profile-avatar .avatar-initial');
            const fileInput = document.getElementById('profile_picture');
            const removeBtn = document.getElementById('remove-btn');
            const removeFlag = document.getElementById('remove_photo_input');

            // Set hidden flag so backend knows to delete
            removeFlag.value = "1";
            
            // Clear file input
            fileInput.value = "";
            
            // Hide preview, show initial
            preview.src = "";
            preview.classList.add('hidden');
            if(initial) initial.classList.remove('hidden');
            
            // Hide remove button
            removeBtn.classList.add('hidden');
        }
    </script>
@endsection
