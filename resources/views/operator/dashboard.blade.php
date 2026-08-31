@extends('layouts.app')

@section('title', 'Dashboard Global')

@section('content')
<!-- PROTOTYPE DATA — REPLACE WITH BACKEND METRICS -->
<div class="space-y-6 md:space-y-8 relative z-10">
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
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
        <x-dashboard.stat-card title="Total Laporan" :value="$metrics['total'] ?? 0" status="total" />
        <x-dashboard.stat-card title="Pending" :value="$metrics['pending'] ?? 0" status="pending" />
        <x-dashboard.stat-card title="Diproses" :value="$metrics['diproses'] ?? 0" status="diproses" />
        <x-dashboard.stat-card title="Perbaikan" :value="$metrics['perlu_perbaikan'] ?? 0" status="perlu_perbaikan" />
        <x-dashboard.stat-card title="Disetujui" :value="$metrics['disetujui'] ?? 0" status="disetujui" />
        <x-dashboard.stat-card title="Ditolak" :value="$metrics['ditolak'] ?? 0" status="ditolak" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-6 md:space-y-8">
            <!-- Chart Placeholder -->
            <x-ui.card>
                <x-slot name="header">
                    <h2 class="text-lg font-semibold text-text">Sebaran Laporan per Kabupaten/Kota</h2>
                </x-slot>
                <div class="bg-white p-5">
                    @if(isset($districtStatistics) && $districtStatistics->count() > 0)
                        @php
                            $maxTotal = $districtStatistics->max('total');
                            $sortedDistricts = $districtStatistics->sortByDesc('total');
                        @endphp
                        <ul class="space-y-4 max-h-[350px] overflow-y-auto pr-2">
                            @foreach($sortedDistricts as $stat)
                            @php
                                $widthPercentage = $maxTotal > 0 ? ($stat->total / $maxTotal * 100) : 0;
                                // Minimal visual width for non-zero items
                                if($stat->total > 0 && $widthPercentage < 5) $widthPercentage = 5;
                            @endphp
                            <li>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="font-medium text-gray-700">{{ $stat->district }}</span>
                                    <span class="font-bold text-gray-900">{{ $stat->total }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3">
                                    <div class="bg-primary h-3 rounded-full transition-all duration-500" style="width: {{ $widthPercentage }}%"></div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-sm font-medium text-gray-500">Belum ada data laporan untuk ditampilkan.</p>
                        </div>
                    @endif
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
                    @php
                        $act = strtolower($activity->activity);
                        if (str_contains($act, 'login')) {
                            $iconColor = 'bg-blue-100 text-blue-600';
                            $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>';
                        } elseif (str_contains($act, 'logout')) {
                            $iconColor = 'bg-slate-100 text-slate-600';
                            $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>';
                        } elseif (str_contains($act, 'membuat')) {
                            $iconColor = 'bg-indigo-100 text-indigo-600';
                            $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>';
                        } elseif (str_contains($act, 'perbaikan') || str_contains($act, 'update')) {
                            $iconColor = 'bg-amber-100 text-amber-600';
                            $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>';
                        } elseif (str_contains($act, 'verifikasi') || str_contains($act, 'setuju')) {
                            $iconColor = 'bg-green-100 text-green-600';
                            $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>';
                        } elseif (str_contains($act, 'tolak')) {
                            $iconColor = 'bg-red-100 text-red-600';
                            $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        } elseif (str_contains($act, 'upload') || str_contains($act, 'dokumen')) {
                            $iconColor = 'bg-purple-100 text-purple-600';
                            $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>';
                        } else {
                            $iconColor = 'bg-gray-100 text-gray-600';
                            $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        }
                    @endphp
                    <!-- Activity Item -->
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $iconColor }} flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
                        </div>
                        <div>
                            <p class="text-sm text-text font-medium">{{ $activity->activity }}</p>
                            <p class="text-xs text-text-secondary mt-0.5">Oleh: {{ $activity->user->full_name }} &bull; {{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas sistem.</p>
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
                    
                    <!-- Filter UI (Redirects to Monitoring) -->
                    <form action="{{ route('operator.monitoring') }}" method="GET">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 bg-gray-50/50 p-3 rounded-lg border border-gray-100">
                            <div class="relative">
                                <input type="text" name="search" placeholder="Cari nomor/nama/NIK..." class="w-full text-sm px-3 py-2 pl-9 border border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <div>
                                <select name="district_id" class="w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-white">
                                    <option value="">Semua Wilayah</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">
                                            {{ $district->name }} ({{ $district->reports_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="status" class="w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-white">
                                    <option value="">Semua Status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Diproses">Diproses</option>
                                    <option value="Disetujui">Disetujui</option>
                                    <option value="Ditolak">Ditolak</option>
                                    <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="w-full px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary-dark transition-colors">Lihat Laporan</button>
                            </div>
                        </div>
                    </form>
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
