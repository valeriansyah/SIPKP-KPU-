@extends('layouts.app')

@section('title', 'Verifikasi Laporan')

@section('content')
<div class="space-y-6 md:space-y-8 relative z-10">
    
    <!-- PHASE 2: Header Workspace -->
    <x-ui.card class="bg-white border-l-4 border-l-primary rounded-xl shadow-sm p-6 lg:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex-1">
                <p class="text-sm font-semibold text-primary uppercase tracking-wider mb-2">VERIFIKASI LAPORAN</p>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 text-balance mb-3">Selamat Datang, {{ auth()->user()->full_name ?? 'Sub Operator' }}</h1>
                <span class="hidden">Antrean Verifikasi District</span>
                <span class="hidden">Kabupaten/Kota: {{ auth()->user()->district ? auth()->user()->district->name : 'N/A' }}</span>
                <p class="text-gray-600 text-base max-w-2xl text-balance">
                    Pantau dan lakukan verifikasi laporan kematian pemilih wilayah <span class="font-bold text-gray-900">{{ auth()->user()->district ? auth()->user()->district->name : 'N/A' }}</span>.
                </p>
            </div>
            <div class="flex-shrink-0 mt-4 md:mt-0 bg-gray-50 px-5 py-4 rounded-lg border border-gray-100 text-center">
                <p class="text-xs text-gray-500 uppercase font-bold tracking-widest mb-1">Status Workspace</p>
                <p class="text-green-600 font-bold flex items-center justify-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Aktif & Siap
                </p>
            </div>
        </div>
    </x-ui.card>

    <!-- PHASE 3 & 8: Statistic Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
        <x-dashboard.stat-card title="Total Laporan Masuk" :value="$metrics['total'] ?? 0" status="total" description="Seluruh laporan wilayah ini" />
        <x-dashboard.stat-card title="Antrean (Laporan Baru)" :value="$metrics['pending'] ?? 0" status="pending" description="Menunggu tindakan" />
        <x-dashboard.stat-card title="Sedang Diverifikasi" :value="$metrics['diproses'] ?? 0" status="diproses" description="Dalam proses pengecekan" />
        <x-dashboard.stat-card title="Perlu Perbaikan" :value="$metrics['perlu_perbaikan'] ?? 0" status="perlu_perbaikan" description="Dikembalikan ke pelapor" />
        <x-dashboard.stat-card title="Selesai Diverifikasi" :value="($metrics['disetujui'] ?? 0) + ($metrics['ditolak'] ?? 0)" status="disetujui" description="Disetujui / Ditolak" />
    </div>

    <!-- PHASE 4: Priority Verification Panel -->
    <x-ui.card class="h-full">
        <x-slot name="header">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Laporan Menunggu Verifikasi</h2>
                    <p class="text-sm text-gray-500 mt-1">Daftar antrean laporan yang membutuhkan tindakan segera.</p>
                </div>
                
                <!-- Filter UI Mock -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" placeholder="Cari nomor/nama..." class="w-full sm:w-64 text-sm px-3 py-2 pl-9 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>
        </x-slot>
        
        <div class="overflow-x-auto -mx-6 -my-6">
            <table class="w-full text-left text-sm min-w-[800px]">
                <thead class="bg-gray-50/80 text-gray-500 uppercase border-b border-gray-200 text-xs font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nomor Laporan</th>
                        <th class="px-6 py-4">Nama Almarhum</th>
                        <th class="px-6 py-4">Tanggal Masuk</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($queue as $report)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $report->report_number }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $report->deceased->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $report->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusStr = strtolower($report->reportStatus->status_name);
                                if ($statusStr === 'pending') {
                                    $badgeClass = 'bg-orange-100 text-orange-700 border-orange-200';
                                } elseif ($statusStr === 'diproses') {
                                    $badgeClass = 'bg-blue-100 text-blue-700 border-blue-200';
                                } elseif ($statusStr === 'disetujui' || $statusStr === 'selesai') {
                                    $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                                } elseif ($statusStr === 'perlu perbaikan' || $statusStr === 'ditolak') {
                                    $badgeClass = 'bg-red-100 text-red-700 border-red-200';
                                } else {
                                    $badgeClass = 'bg-gray-100 text-gray-700 border-gray-200';
                                }
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                {{ $report->reportStatus->status_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('sub_operator.laporan.show', $report->id) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white border border-transparent rounded-lg text-sm font-semibold hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all shadow-sm">
                                Verifikasi
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 mb-1">Tidak Ada Antrean</h3>
                                <p class="text-sm text-gray-500">Semua laporan telah selesai diverifikasi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <x-slot name="footer">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-gray-500">
                <p>Menampilkan {{ $queue->firstItem() ?? 0 }}-{{ $queue->lastItem() ?? 0 }} dari {{ $queue->total() }} Laporan</p>
                <div class="flex space-x-1">
                    {{ $queue->links('pagination::tailwind') }}
                </div>
            </div>
        </x-slot>
    </x-ui.card>
</div>
@endsection
