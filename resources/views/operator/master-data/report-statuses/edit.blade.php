@extends('layouts.app')

@section('title', 'Ubah Status Laporan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-text">Ubah Deskripsi Status Laporan</h1>
        <p class="text-text-secondary mt-1">Perbarui deskripsi status. Nama Key Status tidak dapat diubah demi keamanan alur.</p>
    </div>
    <a href="{{ route('operator.master-data.report-statuses.index') }}" class="text-primary hover:text-primary-dark font-medium text-sm flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali
    </a>
</div>

<x-ui.card class="bg-white border border-gray-100 shadow-sm max-w-2xl">
    <form action="{{ route('operator.master-data.report-statuses.update', $reportStatus->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-text mb-1">Status Key (Canonical)</label>
                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-md text-gray-500 font-mono sm:text-sm cursor-not-allowed">
                    {{ $reportStatus->status_name }}
                </div>
                <p class="mt-1 text-xs text-text-secondary">Identifier internal. Tidak dapat diubah.</p>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-text mb-1">Deskripsi Tampil</label>
                <textarea name="description" id="description" rows="4"
                    class="w-full px-3 py-2 border {{ $errors->has('description') ? 'border-red-500' : 'border-gray-300' }} rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                    placeholder="Masukkan deskripsi untuk ditampilkan ke pengguna.">{{ old('description', $reportStatus->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('operator.master-data.report-statuses.index') }}">
                <x-ui.button type="button" variant="secondary" class="h-10 px-4">Batal</x-ui.button>
            </a>
            <x-ui.button type="submit" variant="primary" class="h-10 px-6">Perbarui Deskripsi</x-ui.button>
        </div>
    </form>
</x-ui.card>
@endsection
