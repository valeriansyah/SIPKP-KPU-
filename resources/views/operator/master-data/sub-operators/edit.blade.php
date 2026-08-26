@extends('layouts.app')

@section('title', 'Ubah Sub Operator')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-text">Ubah Akun Sub Operator</h1>
        <p class="text-text-secondary mt-1">Perbarui data atau wilayah tugas verifikator.</p>
    </div>
    <a href="{{ route('operator.master-data.sub-operators.index') }}" class="text-primary hover:text-primary-dark font-medium text-sm flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>
</div>

<x-ui.card class="bg-white border border-gray-100 shadow-sm max-w-2xl">
    <form action="{{ route('operator.master-data.sub-operators.update', $subOperator->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-text mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $subOperator->full_name) }}" required
                        class="w-full px-3 py-2 border {{ $errors->has('full_name') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    @error('full_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-text mb-1">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email', $subOperator->email) }}" required
                        class="w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="phone_number" class="block text-sm font-medium text-text mb-1">Nomor Telepon / WhatsApp</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $subOperator->phone_number) }}"
                    class="w-full px-3 py-2 border {{ $errors->has('phone_number') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                    placeholder="Contoh: 081234567890">
                @error('phone_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="district_id" class="block text-sm font-medium text-text mb-1">Wilayah Penugasan (District) <span class="text-red-500">*</span></label>
                <select name="district_id" id="district_id" required
                    class="w-full px-3 py-2 bg-white border {{ $errors->has('district_id') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    <option value="">-- Pilih Wilayah Kabupaten/Kota --</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ old('district_id', $subOperator->district_id) == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
                @error('district_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-text mb-1">Kata Sandi (Opsional)</label>
                <input type="password" name="password" id="password" minlength="8"
                    class="w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                    placeholder="Kosongkan jika tidak ingin mengubah sandi">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $subOperator->is_active) ? 'checked' : '' }}
                        class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="is_active" class="font-medium text-text">Akun Aktif</label>
                    <p class="text-text-secondary">Akun dapat digunakan untuk login.</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('operator.master-data.sub-operators.index') }}">
                <x-ui.button type="button" variant="secondary" class="h-10 px-4">Batal</x-ui.button>
            </a>
            <x-ui.button type="submit" variant="primary" class="h-10 px-6">Perbarui Akun</x-ui.button>
        </div>
    </form>
</x-ui.card>
@endsection
