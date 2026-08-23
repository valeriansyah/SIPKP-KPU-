@extends('layouts.app')

@section('title', 'Dashboard Saya')

@section('content')
<!-- PROTOTYPE DATA — REPLACE WITH BACKEND METRICS -->
<div>
    
    <!-- SECTION 1: Welcome Panel -->
    <x-ui.card class="bg-white border-l-4 border-l-primary shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex-1">
                <p class="text-sm font-medium text-text-secondary uppercase tracking-wider mb-1">Dashboard Pelapor</p>
                <h1 class="text-2xl md:text-3xl font-bold text-text text-balance">Selamat Datang, {{ auth()->user()->full_name ?? 'Pelapor' }}</h1>
                <p class="text-text-secondary mt-2 text-base max-w-2xl text-balance">
                    Pantau status laporan kematian pemilih yang telah Anda ajukan. 
                    Pastikan untuk segera memperbaiki laporan apabila status menunjukkan "Perlu Perbaikan".
                </p>
            </div>
            <div class="flex-shrink-0">
                <x-ui.button variant="primary" class="shadow-sm w-full md:w-auto h-11 px-6">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Laporan Baru
                </x-ui.button>
            </div>
        </div>
    </x-ui.card>

    <!-- SECTION 2: Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mt-6 md:mt-8">
        <x-dashboard.stat-card title="Total Laporan" :value="$metrics['total'] ?? 0" status="total" />
        <x-dashboard.stat-card title="Pending" :value="$metrics['pending'] ?? 0" status="pending" />
        <x-dashboard.stat-card title="Diproses" :value="$metrics['diproses'] ?? 0" status="diproses" />
        <x-dashboard.stat-card title="Perlu Perbaikan" :value="$metrics['perlu_perbaikan'] ?? 0" status="perlu_perbaikan" />
        <x-dashboard.stat-card title="Selesai" :value="($metrics['disetujui'] ?? 0) + ($metrics['ditolak'] ?? 0)" status="disetujui" />
    </div>

    <!-- SECTION 3: Recent Reports -->
    <div class="mt-6 md:mt-8">
        <x-ui.card>
            <x-slot name="header">
                <div class="flex items-center justify-between w-full">
                    <h2 class="text-lg font-semibold text-text">Laporan Terbaru Saya</h2>
                    <a href="{{ route('pelapor.laporan.index') }}" class="text-sm font-medium text-primary hover:text-primary-dark hover:underline transition-colors">Lihat Semua</a>
                </div>
            </x-slot>
            
            @if(isset($recentReports) && $recentReports->isNotEmpty())
            <div class="overflow-x-auto -mx-6 -my-6">
                <table class="w-full text-left text-sm text-text min-w-[700px]">
                    <thead class="bg-gray-50 text-text-secondary uppercase border-b border-gray-200 text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-4 w-[20%]">No. Laporan</th>
                            <th class="px-6 py-4 w-[25%]">Nama Almarhum</th>
                            <th class="px-6 py-4 w-[15%]">Tanggal</th>
                            <th class="px-6 py-4 w-[15%]">Status</th>
                            <th class="px-6 py-4 w-[25%] text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentReports as $report)
                        <tr class="hover:bg-gray-50 transition-colors @if($report->reportStatus->status_name === 'Perlu Perbaikan') bg-orange-50/20 hover:bg-orange-50/50 @endif">
                            <td class="px-6 py-4 font-medium text-primary">{{ $report->report_number }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $report->deceased->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $report->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4"><x-reports.status-badge :status="$report->reportStatus->status_name" /></td>
                            <td class="px-6 py-4 text-right @if($report->reportStatus->status_name === 'Perlu Perbaikan') space-y-2 sm:space-y-0 sm:space-x-2 flex flex-col sm:flex-row justify-end @endif">
                                <a href="{{ route('pelapor.laporan.show', $report->id) }}">
                                    <x-ui.button size="sm" variant="outline" class="w-full sm:w-auto">Lihat Detail</x-ui.button>
                                </a>
                                @if($report->reportStatus->status_name === 'Perlu Perbaikan')
                                <a href="{{ route('pelapor.laporan.edit', $report->id) }}">
                                    <x-ui.button size="sm" variant="warning" class="w-full sm:w-auto">Perbaiki Laporan</x-ui.button>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <!-- Empty State Preview -->
            <x-ui.empty-state 
                title="Belum Ada Laporan"
                description="Anda belum mengirimkan laporan kematian pemilih."
            >
                <x-slot name="icon">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </x-slot>
                <x-slot name="action">
                    <x-ui.button variant="primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Laporan Baru
                    </x-ui.button>
                </x-slot>
            </x-ui.empty-state>
            @endif
        </x-ui.card>
    </div>
</div>
@endsection
