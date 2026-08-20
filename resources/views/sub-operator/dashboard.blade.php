@extends('layouts.app')

@section('title', 'Dashboard Wilayah')

@section('content')
<!-- PROTOTYPE DATA — REPLACE WITH BACKEND METRICS -->
<div class="space-y-6 md:space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-text">Antrean Verifikasi District</h1>
            <p class="text-text-secondary mt-1">Mengelola antrean verifikasi untuk wilayah tugas Anda.</p>
        </div>
        <div class="text-sm bg-primary/10 text-primary px-4 py-2 rounded-md border border-primary/20 flex flex-col items-end">
            <span class="text-xs uppercase tracking-wider text-text-secondary font-semibold">Wilayah Kerja</span>
            <span class="font-bold flex items-center gap-1 mt-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Kabupaten/Kota: {{ auth()->user()->district ? auth()->user()->district->name : 'N/A' }}
            </span>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- TOTAL / LAPORAN MASUK -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-primary shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Laporan Masuk</p>
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['total'] ?? 0 }}</h3>
            </div>
        </div>
        
        <!-- PENDING -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-amber-500 shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Antrean Verifikasi</p>
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['pending'] ?? 0 }}</h3>
                <p class="text-xs text-gray-500 mt-1">Pending</p>
            </div>
        </div>

        <!-- DIPROSES -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-blue-600 shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Sedang Diproses</p>
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $metrics['diproses'] ?? 0 }}</h3>
            </div>
        </div>

        <!-- SELESAI -->
        <div class="bg-white border border-gray-200 border-l-4 border-l-green-600 shadow-sm rounded-md p-4 flex flex-col justify-between min-h-[120px]">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</p>
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ ($metrics['disetujui'] ?? 0) + ($metrics['ditolak'] ?? 0) }}</h3>
                <p class="text-xs text-gray-500 mt-1">Setuju/Tolak</p>
            </div>
        </div>
    </div>

    <!-- Verification Queue List -->
    <x-ui.card>
        <x-slot name="header">
            <div class="flex flex-col gap-4">
                <h2 class="text-lg font-semibold text-text">Daftar Antrean Verifikasi Laporan</h2>
                
                <!-- Filter UI Mock -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50/50 p-3 rounded-lg border border-gray-100">
                    <div class="relative">
                        <input type="text" placeholder="Cari nomor/nama..." class="w-full text-sm px-3 py-2 pl-9 border border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <div>
                        <select disabled class="w-full text-sm px-3 py-2 border border-gray-200 bg-gray-100 text-gray-500 rounded-md cursor-not-allowed">
                            <option>{{ auth()->user()->district ? auth()->user()->district->name : 'Semua Kabupaten/Kota' }}</option>
                        </select>
                    </div>
                    <div>
                        <select class="w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-white">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="diproses">Diproses</option>
                            <option value="perlu_perbaikan">Perlu Perbaikan</option>
                        </select>
                    </div>
                    <div>
                        <input type="date" class="w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary text-gray-600">
                    </div>
                </div>
            </div>
        </x-slot>
        
        <div class="overflow-x-auto -mx-6 -my-6 mt-0">
            <table class="w-full text-left text-sm text-text min-w-[700px]">
                <thead class="bg-gray-50 text-text-secondary uppercase border-b border-gray-200 text-xs font-semibold">
                    <tr>
                        <th class="px-6 py-4 w-[20%]">No. Laporan</th>
                        <th class="px-6 py-4 w-[25%]">Nama Almarhum</th>
                        <th class="px-6 py-4 w-[20%]">Tgl Masuk</th>
                        <th class="px-6 py-4 w-[20%]">Status</th>
                        <th class="px-6 py-4 w-[15%] text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($queue as $report)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-primary">{{ $report->report_number }}</td>
                        <td class="px-6 py-4 text-gray-900">{{ $report->deceased->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $report->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4"><x-reports.status-badge :status="$report->reportStatus->status_name" /></td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('sub_operator.laporan.show', $report->id) }}">
                                <x-ui.button size="sm" variant="outline" class="w-full sm:w-auto">Lihat Detail</x-ui.button>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Tidak ada antrean laporan saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <x-slot name="footer">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-text-secondary">
                <p>Menampilkan {{ $queue->firstItem() ?? 0 }}-{{ $queue->lastItem() ?? 0 }} dari {{ $queue->total() }} Laporan</p>
                <div class="flex space-x-1">
                    {{ $queue->links('pagination::tailwind') }}
                </div>
            </div>
        </x-slot>
    </x-ui.card>
</div>
@endsection
