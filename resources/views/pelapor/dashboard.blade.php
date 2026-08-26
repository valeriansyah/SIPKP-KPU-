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
        <x-dashboard.stat-card title="Total Laporan" :value="$metrics['total'] ?? 0" status="total" description="Semua laporan Anda" />
        <x-dashboard.stat-card title="Pending" :value="$metrics['pending'] ?? 0" status="pending" description="Menunggu verifikasi" />
        <x-dashboard.stat-card title="Diproses" :value="$metrics['diproses'] ?? 0" status="diproses" description="Sedang ditindaklanjuti" />
        <x-dashboard.stat-card title="Perlu Perbaikan" :value="$metrics['perlu_perbaikan'] ?? 0" status="perlu_perbaikan" description="Butuh perbaikan data" />
        <x-dashboard.stat-card title="Selesai" :value="($metrics['disetujui'] ?? 0) + ($metrics['ditolak'] ?? 0)" status="disetujui" description="Disetujui / Ditolak" />
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
        <div class="flex flex-col gap-6">
            <x-ui.card>
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

            <!-- SUB OPERATOR CONTACT CARD -->
            <x-ui.card class="bg-blue-50/50 border border-blue-100">
                <x-slot name="header">
                    <h2 class="text-lg font-bold text-blue-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Butuh Bantuan?
                    </h2>
                </x-slot>
                
                <div class="p-2">
                    <p class="text-sm text-gray-700 mb-4 text-balance">Jika Anda mengalami kendala dalam pelaporan atau diminta melakukan perbaikan, silakan hubungi Sub Operator wilayah Anda.</p>
                    
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        @if(auth()->user()->district_id === null)
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 mb-1">Kontak Belum Tersedia</p>
                                    <p class="text-xs text-gray-600 text-balance mb-2">Kontak Sub Operator wilayah Anda belum tersedia. Lengkapi Kabupaten/Kota pada Profil Saya untuk menampilkan kontak petugas wilayah.</p>
                                    <a href="{{ route('pelapor.profile.edit') }}" class="inline-flex items-center text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                                        Lengkapi Profil <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            </div>
                        @elseif(isset($subOperator) && $subOperator)
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold shrink-0">
                                    {{ strtoupper(substr($subOperator->full_name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $subOperator->full_name }}</p>
                                    <p class="text-xs text-gray-500 mb-2 truncate">{{ $subOperator->district->name ?? 'Wilayah Tidak Diketahui' }}</p>
                                    
                                    @if($subOperator->phone_number && $subOperator->phone_number !== '-')
                                        <div class="flex items-center gap-2 text-sm font-medium text-gray-800">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            {{ $subOperator->phone_number }}
                                        </div>
                                        
                                        @php
                                            // Normalize phone number for WhatsApp: replace leading 0 with 62, remove non-digits
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $subOperator->phone_number);
                                            if (str_starts_with($cleanPhone, '0')) {
                                                $waNumber = '62' . substr($cleanPhone, 1);
                                            } else {
                                                $waNumber = $cleanPhone;
                                            }
                                        @endphp
                                        
                                        @if($waNumber)
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center justify-center w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 0C5.385 0 0 5.385 0 12.031c0 2.127.554 4.195 1.602 6.012L.15 23.518l5.625-1.474a12.04 12.04 0 006.255 1.737h.005c6.645 0 12.03-5.385 12.03-12.031S18.677 0 12.031 0zM12.031 21.758h-.005a10.02 10.02 0 01-5.111-1.39l-.367-.217-3.8.995.998-3.705-.238-.379A10.02 10.02 0 012.012 12.03C2.012 6.505 6.505 2.012 12.031 2.012c5.526 0 10.02 4.493 10.02 10.019s-4.494 10.018-10.02 10.018zm5.503-7.514c-.302-.151-1.785-.881-2.062-.981-.277-.101-.479-.151-.68.151-.202.302-.78 1.031-.957 1.233-.176.202-.353.226-.655.075-.302-.151-1.275-.47-2.428-1.5-.898-.802-1.504-1.792-1.68-2.094-.176-.302-.019-.465.132-.616.136-.136.302-.353.453-.53.151-.176.202-.302.302-.504.101-.202.05-.378-.025-.53-.075-.151-.68-1.639-.932-2.244-.246-.59-.496-.51-.68-.52-.176-.01-.378-.01-.58-.01-.202 0-.53.076-.807.378-.277.302-1.058 1.031-1.058 2.515 0 1.484 1.083 2.918 1.234 3.12.151.202 2.128 3.245 5.151 4.545.719.31 1.28.495 1.718.634.721.23 1.378.197 1.895.12.58-.087 1.785-.73 2.037-1.434.252-.705.252-1.31.176-1.434-.076-.126-.277-.202-.58-.353z"/></svg>
                                            Hubungi via WhatsApp
                                        </a>
                                        @endif
                                    @else
                                        <p class="text-xs text-orange-600 font-medium italic mt-1">Nomor telepon belum tersedia.</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 mb-1">Kontak Belum Tersedia</p>
                                    <p class="text-xs text-gray-600 text-balance">Kontak Sub Operator wilayah Anda belum tersedia. Silakan menghubungi KPU Kabupaten/Kota setempat.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
