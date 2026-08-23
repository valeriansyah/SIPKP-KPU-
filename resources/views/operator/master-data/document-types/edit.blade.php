@extends('layouts.app')

@section('title', 'Ubah Jenis Dokumen')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-text">Ubah Jenis Dokumen</h1>
        <p class="text-text-secondary mt-1">Perbarui data jenis dokumen lampiran.</p>
    </div>
    <a href="{{ route('operator.master-data.document-types.index') }}" class="text-primary hover:text-primary-dark font-medium text-sm flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>
</div>

<x-ui.card class="bg-white border border-gray-100 shadow-sm max-w-2xl">
    <form action="{{ route('operator.master-data.document-types.update', $documentType->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-text mb-1">Nama Dokumen <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $documentType->name) }}" required
                    class="w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                    placeholder="Contoh: KTP Pelapor">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-text mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-3 py-2 border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                    placeholder="Contoh: Fotokopi KTP pelapor yang masih berlaku.">{{ old('description', $documentType->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input type="checkbox" name="is_required" id="is_required" value="1" {{ old('is_required', $documentType->is_required) ? 'checked' : '' }}
                        class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="is_required" class="font-medium text-text">Dokumen Wajib</label>
                    <p class="text-text-secondary">Centang jika dokumen ini wajib dilampirkan oleh Pelapor.</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('operator.master-data.document-types.index') }}">
                <x-ui.button type="button" variant="secondary" class="h-10 px-4">Batal</x-ui.button>
            </a>
            <x-ui.button type="submit" variant="primary" class="h-10 px-6">Perbarui Data</x-ui.button>
        </div>
    </form>
</x-ui.card>
@endsection
