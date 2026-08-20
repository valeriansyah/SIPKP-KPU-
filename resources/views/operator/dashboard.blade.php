@extends('layouts.app')

@section('title', 'Dashboard Global')

@section('content')
<!-- PROTOTYPE DATA — REPLACE WITH BACKEND METRICS -->
<div class="space-y-6 md:space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-text">Monitoring Global Provinsi</h1>
            <p class="text-text-secondary mt-1">Pemantauan seluruh data pelaporan kematian KPU Sumatera Selatan.</p>
        </div>
        <div class="text-sm bg-info/10 text-info px-4 py-2 rounded-md border border-info/20 flex flex-col items-end">
            <span class="text-xs uppercase tracking-wider text-info/80 font-semibold">Hak Akses</span>
            <span class="font-bold flex items-center gap-1 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Operator (Read-Only)
            </span>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- TOTAL -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-primary shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Laporan</p>
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['total'] ?? 0 }}</h3>
            </div>
        </div>
        
        <!-- PENDING -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-amber-500 shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</p>
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['pending'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- DIPROSES -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-blue-600 shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Diproses</p>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['diproses'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- PERLU PERBAIKAN -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-orange-500 shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Perbaikan</p>
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['perlu_perbaikan'] ?? 0 }}</h3>
            </div>
        </div>
        
        <!-- DISETUJUI -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-green-600 shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui</p>
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['disetujui'] ?? 0 }}</h3>
            </div>
        </div>
        
        <!-- DITOLAK -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-red-600 shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Ditolak</p>
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['ditolak'] ?? 0 }}</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-6 md:space-y-8">
            <!-- Chart Placeholder -->
            <x-ui.card>
                <x-slot name="header">
                    <h2 class="text-lg font-semibold text-text">Sebaran Laporan per Kabupaten/Kota</h2>
                </x-slot>
                <div class="h-64 sm:h-80 bg-gray-50 rounded-md border border-dashed border-gray-300 flex items-center justify-center">
                    <div class="text-center w-full px-4">
                        @if(isset($districtStatistics) && $districtStatistics->count() > 0)
                        <ul class="text-left text-sm divide-y max-h-64 overflow-y-auto w-full max-w-sm mx-auto">
                            @foreach($districtStatistics as $stat)
                            <li class="py-2 flex justify-between"><span>{{ $stat->district }}</span> <span class="font-bold">{{ $stat->total }}</span></li>
                            @endforeach
                        </ul>
                        @else
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        <p class="text-sm font-medium text-gray-500">Area Bar Chart Provinsi</p>
                        <p class="text-xs text-gray-400 mt-1">Belum ada data untuk visualisasi chart</p>
                        @endif
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Sidebar / Activity Area -->
        <div class="space-y-6 md:space-y-8">
            <x-ui.card>
                <x-slot name="header">
                    <h2 class="text-lg font-semibold text-text">Aktivitas Sistem Terakhir</h2>
                </x-slot>
                
                <div class="space-y-4">
                    @forelse($activities ?? [] as $activity)
                    <!-- Activity Item -->
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-text"><span class="font-medium">{{ $activity->user->full_name }}</span> {{ $activity->activity }}</p>
                            <p class="text-xs text-text-secondary mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas.</p>
                    @endforelse
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100 text-center">
                    <a href="{{ route('operator.monitoring') }}" class="text-sm font-medium text-primary hover:text-primary-dark hover:underline transition-colors">Lihat Semua Aktivitas</a>
                </div>
            </x-ui.card>
        </div>
    </div>

    <!-- Data Table (Full Width) -->
    <div class="mt-6 md:mt-8">
        <x-ui.card>
            <x-slot name="header">
                <div class="flex flex-col gap-4">
                    <h2 class="text-lg font-semibold text-text">Monitoring Laporan Terkini</h2>
                    
                    <!-- Filter UI Mock -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50/50 p-3 rounded-lg border border-gray-100">
                        <div class="relative">
                            <input type="text" placeholder="Cari nomor/nama..." class="w-full text-sm px-3 py-2 pl-9 border border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <div>
                            <select class="w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-white">
                                <option value="">Semua Wilayah</option>
                                <option value="palembang">Kota Palembang</option>
                                <option value="lubuklinggau">Kota Lubuklinggau</option>
                            </select>
                        </div>
                        <div>
                            <select class="w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-white">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="diproses">Diproses</option>
                                <option value="disetujui">Disetujui</option>
                            </select>
                        </div>
                        <div>
                            <input type="date" class="w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary text-gray-600">
                        </div>
                    </div>
                </div>
            </x-slot>
            
            <div class="overflow-x-auto -mx-6 -my-6 mt-0">
                <table class="w-full text-left text-sm text-text min-w-[800px]">
                    <thead class="bg-gray-50 text-text-secondary uppercase border-b border-gray-200 text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-4 w-[15%]">No. Laporan</th>
                            <th class="px-6 py-4 w-[20%]">Nama Almarhum</th>
                            <th class="px-6 py-4 w-[20%]">Kabupaten/Kota</th>
                            <th class="px-6 py-4 w-[15%]">Tanggal</th>
                            <th class="px-6 py-4 w-[15%]">Status</th>
                            <th class="px-6 py-4 w-[15%] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentReports ?? [] as $report)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-primary">{{ $report->report_number }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $report->deceased->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $report->deceased->district->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $report->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4"><x-reports.status-badge :status="$report->reportStatus->status_name" /></td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('operator.laporan.show', $report->id) }}">
                                    <x-ui.button size="sm" variant="outline" class="w-full sm:w-auto">Lihat Detail</x-ui.button>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Belum ada laporan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
@endsection
