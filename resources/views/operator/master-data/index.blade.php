@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-text">Master Data</h1>
        <p class="text-text-secondary mt-1">Kelola data referensi utama sistem SIPKP.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- District Card -->
        <x-ui.card class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
            <div class="p-6 lg:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">Kabupaten/Kota</h3>
                        <p class="text-sm text-gray-500">Administrasi Data Wilayah Operasional</p>
                        <div class="flex items-baseline gap-2 mt-4">
                            <div class="text-4xl font-black text-blue-700 tracking-tight">{{ $stats['districts'] }}</div>
                            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Total Entri</div>
                        </div>
                    </div>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-blue-50 rounded-2xl flex flex-shrink-0 items-center justify-center text-blue-600 shadow-inner group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
                <div class="pt-5 border-t border-gray-100">
                    <a href="{{ route('operator.master-data.districts.index') }}" class="block">
                        <button class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white text-blue-600 border border-blue-200 hover:bg-blue-50 hover:border-blue-300 hover:shadow-sm rounded-lg text-sm font-semibold transition-all">
                            Kelola Kabupaten/Kota
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </a>
                </div>
            </div>
        </x-ui.card>

        <!-- Document Type Card -->
        <x-ui.card class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-emerald-500"></div>
            <div class="p-6 lg:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">Jenis Dokumen</h3>
                        <p class="text-sm text-gray-500">Konfigurasi Persyaratan Lampiran Laporan</p>
                        <div class="flex items-baseline gap-2 mt-4">
                            <div class="text-4xl font-black text-emerald-700 tracking-tight">{{ $stats['document_types'] }}</div>
                            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Jenis Aktif</div>
                        </div>
                    </div>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-emerald-50 rounded-2xl flex flex-shrink-0 items-center justify-center text-emerald-600 shadow-inner group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
                <div class="pt-5 border-t border-gray-100">
                    <a href="{{ route('operator.master-data.document-types.index') }}" class="block">
                        <button class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white text-emerald-600 border border-emerald-200 hover:bg-emerald-50 hover:border-emerald-300 hover:shadow-sm rounded-lg text-sm font-semibold transition-all">
                            Kelola Jenis Dokumen
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </a>
                </div>
            </div>
        </x-ui.card>

        <!-- Report Status Card -->
        <x-ui.card class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-amber-500"></div>
            <div class="p-6 lg:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">Status Laporan</h3>
                        <p class="text-sm text-gray-500">Parameter Terminologi Alur Sistem</p>
                        <div class="flex items-baseline gap-2 mt-4">
                            <div class="text-4xl font-black text-amber-600 tracking-tight">{{ $stats['report_statuses'] }}</div>
                            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Status Valid</div>
                        </div>
                    </div>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-amber-50 rounded-2xl flex flex-shrink-0 items-center justify-center text-amber-600 shadow-inner group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                </div>
                <div class="pt-5 border-t border-gray-100">
                    <a href="{{ route('operator.master-data.report-statuses.index') }}" class="block">
                        <button class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white text-amber-600 border border-amber-200 hover:bg-amber-50 hover:border-amber-300 hover:shadow-sm rounded-lg text-sm font-semibold transition-all">
                            Kelola Status Laporan
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </a>
                </div>
            </div>
        </x-ui.card>

        <!-- Sub Operator Card -->
        @can('manage-sub-operator')
        <x-ui.card class="bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-purple-500"></div>
            <div class="p-6 lg:p-8">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1">Sub Operator</h3>
                        <p class="text-sm text-gray-500">Manajemen Akses Pengguna Tingkat Daerah</p>
                        <div class="flex items-baseline gap-2 mt-4">
                            <div class="text-4xl font-black text-purple-700 tracking-tight">{{ $stats['sub_operators'] }}</div>
                            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Akun Aktif</div>
                        </div>
                    </div>
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-purple-50 rounded-2xl flex flex-shrink-0 items-center justify-center text-purple-600 shadow-inner group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="pt-5 border-t border-gray-100">
                    <a href="{{ route('operator.master-data.sub-operators.index') }}" class="block">
                        <button class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white text-purple-600 border border-purple-200 hover:bg-purple-50 hover:border-purple-300 hover:shadow-sm rounded-lg text-sm font-semibold transition-all">
                            Kelola Sub Operator
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </a>
                </div>
            </div>
        </x-ui.card>
        @endcan
    </div>
</div>
@endsection
