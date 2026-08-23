@extends('layouts.app')

@section('title', 'Dashboard Pelapor')

@section('content')
<div class="space-y-6 md:space-y-8">
    
    <!-- PHASE 3: Welcome Panel -->
    <x-ui.card class="bg-gradient-to-r from-red-50 to-white border-l-4 border-l-red-600 rounded-xl shadow-sm overflow-hidden relative">
        <div class="absolute right-0 top-0 opacity-5 pointer-events-none">
            <svg class="w-64 h-64 -mt-10 -mr-10 text-red-600" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg>
        </div>
        <div class="p-6 lg:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm font-bold text-red-600 uppercase tracking-wider">DASHBOARD PELAPOR</p>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 text-balance mb-3">Selamat Datang, {{ auth()->user()->full_name ?? 'Pelapor' }}</h1>
                <p class="text-gray-700 text-base max-w-2xl text-balance">
                    Pantau status laporan kematian pemilih yang telah Anda ajukan melalui SIPKP secara transparan.
                </p>
            </div>
            <div class="flex-shrink-0 mt-4 md:mt-0">
                <a href="{{ route('pelapor.laporan.create') }}" class="inline-block w-full md:w-auto">
                    <button class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg shadow hover:bg-red-700 hover:shadow-md transition-all focus:ring-2 focus:ring-offset-2 focus:ring-red-600 border border-red-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Laporan Baru
                    </button>
                </a>
            </div>
        </div>
    </x-ui.card>

    <!-- PHASE 5: Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
        <x-dashboard.stat-card title="Total Laporan" :value="$metrics['total'] ?? 0" status="total" description="Semua laporan Anda" variant="solid" />
        <x-dashboard.stat-card title="Pending" :value="$metrics['pending'] ?? 0" status="pending" description="Menunggu verifikasi" variant="solid" />
        <x-dashboard.stat-card title="Diproses" :value="$metrics['diproses'] ?? 0" status="diproses" description="Sedang ditindaklanjuti" variant="solid" />
        <x-dashboard.stat-card title="Perlu Perbaikan" :value="$metrics['perlu_perbaikan'] ?? 0" status="perlu_perbaikan" description="Butuh perbaikan data" variant="solid" />
        <x-dashboard.stat-card title="Selesai" :value="($metrics['disetujui'] ?? 0) + ($metrics['ditolak'] ?? 0)" status="disetujui" description="Disetujui / Ditolak" variant="solid" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <!-- PHASE 7: Recent Reports Table -->
        <div class="lg:col-span-2">
            <x-ui.card class="h-full">
                <x-slot name="header">
                    <div class="flex items-center justify-between w-full">
                        <h2 class="text-lg font-bold text-gray-900">Laporan Terbaru Saya</h2>
                        <a href="{{ route('pelapor.laporan.index') }}" class="text-sm font-semibold text-red-600 hover:text-red-700 hover:underline transition-colors">Lihat Semua</a>
                    </div>
                </x-slot>
                
                @if(isset($recentReports) && $recentReports->isNotEmpty())
                <div class="overflow-x-auto -mx-6 -my-6">
                    <table class="w-full text-left text-sm min-w-[600px]">
                        <thead class="bg-gray-50/80 text-gray-500 uppercase border-b border-gray-200 text-xs font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Nomor Laporan</th>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentReports as $report)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $report->report_number }}</td>
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
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('pelapor.laporan.show', $report->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-xs font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-1">Belum Ada Laporan</h3>
                    <p class="text-sm text-gray-500 mb-4">Anda belum mengirimkan laporan kematian pemilih.</p>
                </div>
                @endif
            </x-ui.card>
        </div>

        <!-- PHASE 6: Status Laporan Terakhir -->
        <div>
            <x-ui.card class="h-full">
                <x-slot name="header">
                    <h2 class="text-lg font-bold text-gray-900">Status Laporan Terakhir</h2>
                </x-slot>
                
                <div class="p-2">
                    @php
                        $latestReport = isset($recentReports) ? $recentReports->first() : null;
                    @endphp
                    
                    @if($latestReport)
                        <div class="mb-6">
                            <p class="text-sm text-gray-500 mb-1">Nomor Laporan</p>
                            <p class="font-bold text-gray-900 text-lg">{{ $latestReport->report_number }}</p>
                        </div>
                        
                        @php
                            $status = strtolower($latestReport->reportStatus->status_name);
                            // Simple logic to determine progress stages
                            $isDiajukan = true;
                            $isDiverifikasi = in_array($status, ['diproses', 'disetujui', 'ditolak', 'perlu perbaikan']);
                            $isSelesai = in_array($status, ['disetujui', 'ditolak']);
                            
                            // Step 1: Diajukan
                            $step1Color = $isDiajukan ? 'text-red-600' : 'text-gray-300';
                            $step1Bg = $isDiajukan ? 'bg-red-600' : 'bg-gray-200';
                            $line1Color = $isDiverifikasi ? 'border-red-600' : 'border-gray-200';
                            
                            // Step 2: Diverifikasi
                            $step2Color = 'text-gray-400';
                            $step2Bg = 'bg-gray-200';
                            $line2Color = 'border-gray-200';
                            
                            if ($isDiverifikasi) {
                                if ($status === 'perlu perbaikan') {
                                    $step2Color = 'text-orange-500';
                                    $step2Bg = 'bg-orange-500';
                                    $line2Color = 'border-gray-200'; // Doesn't proceed to selesai yet
                                } else {
                                    $step2Color = 'text-blue-600';
                                    $step2Bg = 'bg-blue-600';
                                    $line2Color = $isSelesai ? 'border-blue-600' : 'border-gray-200';
                                }
                            }
                            
                            // Step 3: Selesai
                            $step3Color = 'text-gray-400';
                            $step3Bg = 'bg-white border-2 border-gray-300';
                            
                            if ($isSelesai) {
                                if ($status === 'ditolak') {
                                    $step3Color = 'text-red-800';
                                    $step3Bg = 'bg-red-800 border-none';
                                } else {
                                    $step3Color = 'text-green-600';
                                    $step3Bg = 'bg-green-600 border-none';
                                }
                            }
                        @endphp
                        
                        <div class="relative pl-6 space-y-8 before:absolute before:inset-0 before:ml-[11px] before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
                            <!-- Step 1: Diajukan -->
                            <div class="relative flex items-center justify-between">
                                <div class="absolute left-[-29px] w-4 h-4 rounded-full {{ $step1Bg }} ring-4 ring-white z-10 flex items-center justify-center">
                                </div>
                                <div>
                                    <p class="font-semibold {{ $step1Color }}">Diajukan</p>
                                    <p class="text-xs text-gray-500 mt-1">Laporan berhasil dikirim</p>
                                </div>
                            </div>
                            
                            <!-- Line 1 -->
                            <div class="absolute left-[-23px] top-[16px] bottom-[auto] h-10 border-l-2 {{ $line1Color }} z-0"></div>
                            
                            <!-- Step 2: Diverifikasi -->
                            <div class="relative flex items-center justify-between">
                                <div class="absolute left-[-29px] w-4 h-4 rounded-full {{ $step2Bg }} ring-4 ring-white z-10 flex items-center justify-center">
                                </div>
                                <div>
                                    <p class="font-semibold {{ $step2Color }}">Diverifikasi</p>
                                    <p class="text-xs text-gray-500 mt-1">Pengecekan oleh Sub Operator</p>
                                </div>
                            </div>
                            
                            <!-- Line 2 -->
                            <div class="absolute left-[-23px] top-[76px] bottom-[auto] h-10 border-l-2 {{ $line2Color }} z-0"></div>
                            
                            <!-- Step 3: Selesai -->
                            <div class="relative flex items-center justify-between">
                                <div class="absolute left-[-29px] w-4 h-4 rounded-full {{ $step3Bg }} ring-4 ring-white z-10 flex items-center justify-center">
                                </div>
                                <div>
                                    <p class="font-semibold {{ $step3Color }}">Selesai</p>
                                    <p class="text-xs text-gray-500 mt-1">Keputusan akhir (Setuju/Tolak)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <p class="text-xs text-gray-500 text-center mb-3">Status Saat Ini:</p>
                            @php
                                $finalBadge = 'bg-gray-100 text-gray-700';
                                $finalIcon = '';
                                
                                if ($status === 'disetujui') {
                                    $finalBadge = 'bg-green-100 text-green-700';
                                    $finalIcon = '✓';
                                } elseif ($status === 'ditolak') {
                                    $finalBadge = 'bg-red-100 text-red-800';
                                    $finalIcon = '✕';
                                } elseif ($status === 'pending') {
                                    $finalBadge = 'bg-yellow-100 text-yellow-700';
                                    $finalIcon = '⏳';
                                } elseif ($status === 'diproses') {
                                    $finalBadge = 'bg-blue-100 text-blue-700';
                                    $finalIcon = '🔄';
                                } elseif ($status === 'perlu perbaikan') {
                                    $finalBadge = 'bg-orange-100 text-orange-700';
                                    $finalIcon = '⚠️';
                                }
                            @endphp
                            <div class="text-center">
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full font-bold text-sm {{ $finalBadge }}">
                                    @if($finalIcon)<span>{{ $finalIcon }}</span>@endif
                                    {{ $latestReport->reportStatus->status_name }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="py-12 flex flex-col items-center justify-center text-center">
                            <p class="text-sm font-medium text-gray-500">Belum ada laporan</p>
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
