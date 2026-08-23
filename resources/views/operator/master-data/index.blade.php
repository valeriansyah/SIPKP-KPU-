@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-text">Master Data</h1>
        <p class="text-text-secondary mt-1">Kelola data referensi utama sistem SIPKP.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- District Card -->
        <x-ui.card class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
            <div class="p-5 flex-grow">
                <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center mb-4 text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-text mb-1">Kabupaten/Kota</h3>
                <p class="text-sm text-text-secondary mb-4">Kelola data wilayah operasional pelaporan.</p>
                <div class="text-2xl font-bold text-text mb-1">{{ $stats['districts'] }}</div>
                <div class="text-xs text-text-secondary uppercase tracking-wider">Total Wilayah</div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 mt-auto">
                <a href="{{ route('operator.master-data.districts.index') }}" class="text-primary hover:text-primary-dark font-medium text-sm flex items-center">
                    Kelola Data <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </x-ui.card>

        <!-- Document Type Card -->
        <x-ui.card class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
            <div class="p-5 flex-grow">
                <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center mb-4 text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-text mb-1">Jenis Dokumen</h3>
                <p class="text-sm text-text-secondary mb-4">Kelola persyaratan lampiran laporan.</p>
                <div class="text-2xl font-bold text-text mb-1">{{ $stats['document_types'] }}</div>
                <div class="text-xs text-text-secondary uppercase tracking-wider">Jenis Dokumen</div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 mt-auto">
                <a href="{{ route('operator.master-data.document-types.index') }}" class="text-primary hover:text-primary-dark font-medium text-sm flex items-center">
                    Kelola Data <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </x-ui.card>

        <!-- Report Status Card -->
        <x-ui.card class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
            <div class="p-5 flex-grow">
                <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center mb-4 text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-text mb-1">Status Laporan</h3>
                <p class="text-sm text-text-secondary mb-4">Sesuaikan deskripsi status alur sistem.</p>
                <div class="text-2xl font-bold text-text mb-1">{{ $stats['report_statuses'] }}</div>
                <div class="text-xs text-text-secondary uppercase tracking-wider">Status Workflow</div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 mt-auto">
                <a href="{{ route('operator.master-data.report-statuses.index') }}" class="text-primary hover:text-primary-dark font-medium text-sm flex items-center">
                    Lihat Data <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </x-ui.card>

        <!-- Sub Operator Card -->
        @can('manage-sub-operator')
        <x-ui.card class="bg-white border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
            <div class="p-5 flex-grow">
                <div class="w-12 h-12 bg-primary-light rounded-lg flex items-center justify-center mb-4 text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="text-lg font-semibold text-text mb-1">Sub Operator</h3>
                <p class="text-sm text-text-secondary mb-4">Kelola akun verifikator tiap Kabupaten/Kota.</p>
                <div class="text-2xl font-bold text-text mb-1">{{ $stats['sub_operators'] }}</div>
                <div class="text-xs text-text-secondary uppercase tracking-wider">Akun Aktif</div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100 mt-auto">
                <a href="{{ route('operator.master-data.sub-operators.index') }}" class="text-primary hover:text-primary-dark font-medium text-sm flex items-center">
                    Kelola Akun <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </x-ui.card>
        @endcan
    </div>
</div>
@endsection
