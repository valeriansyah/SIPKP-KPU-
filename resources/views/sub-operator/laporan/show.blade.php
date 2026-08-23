@extends('layouts.app')

@section('title', 'Detail Verifikasi')

@section('content')
<div class="space-y-6 md:space-y-8 max-w-5xl mx-auto">
    <!-- Header Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail & Verifikasi Laporan</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan Nomor: <span class="font-semibold text-gray-900">{{ $report->report_number }}</span></p>
        </div>
        <a href="{{ route('sub_operator.antrean') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Antrean
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Verifikasi Gagal!</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- 1. Informasi Laporan -->
    <x-ui.card class="overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">1. Informasi Laporan</h2>
                <p class="text-sm text-gray-500 mt-1">Masuk pada {{ $report->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
            <div>
                @php
                    $statusStr = strtolower($report->reportStatus->status_name);
                    if ($statusStr === 'pending') $badgeClass = 'bg-orange-100 text-orange-700 border-orange-200';
                    elseif ($statusStr === 'diproses') $badgeClass = 'bg-blue-100 text-blue-700 border-blue-200';
                    elseif ($statusStr === 'disetujui' || $statusStr === 'selesai') $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                    elseif ($statusStr === 'perlu perbaikan' || $statusStr === 'ditolak') $badgeClass = 'bg-red-100 text-red-700 border-red-200';
                    else $badgeClass = 'bg-gray-100 text-gray-700 border-gray-200';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold border {{ $badgeClass }}">
                    Status: {{ $report->reportStatus->status_name }}
                </span>
            </div>
        </div>
        
        <!-- 2. Data Almarhum -->
        <div class="p-6 bg-gray-50/50">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                2. Data Almarhum
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                <div>
                    <dt class="text-sm font-semibold text-gray-500">NIK</dt>
                    <dd class="mt-1 text-base font-medium text-gray-900">{{ $report->deceased->nik }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-500">Nama Lengkap</dt>
                    <dd class="mt-1 text-base font-medium text-gray-900">{{ $report->deceased->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-500">Jenis Kelamin</dt>
                    <dd class="mt-1 text-base font-medium text-gray-900">{{ $report->deceased->gender }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-500">Tempat, Tanggal Lahir</dt>
                    <dd class="mt-1 text-base font-medium text-gray-900">{{ $report->deceased->birth_place }}, {{ \Carbon\Carbon::parse($report->deceased->birth_date)->format('d F Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-500">Tanggal Meninggal</dt>
                    <dd class="mt-1 text-base font-bold text-red-600">{{ \Carbon\Carbon::parse($report->deceased->death_date)->format('d F Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-semibold text-gray-500">Kabupaten/Kota</dt>
                    <dd class="mt-1 text-base font-medium text-gray-900">{{ $report->deceased->district->name }}</dd>
                </div>
            </div>
        </div>

        <!-- 3. Lampiran Dokumen -->
        <div class="p-6 border-t border-gray-100">
            <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                3. Lampiran Dokumen
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @forelse($report->documents as $document)
                    <div class="border border-gray-200 rounded-xl p-4 bg-white flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-start gap-3 mb-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 line-clamp-2">{{ $document->documentType->name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">Dokumen Pendukung</p>
                            </div>
                        </div>
                        
                        <div>
                            @if($document->is_dummy)
                                <span class="block w-full py-2 text-center text-xs font-semibold text-gray-400 bg-gray-50 rounded-lg border border-gray-100">
                                    File demo tidak tersedia
                                </span>
                            @else
                                <a href="{{ route('documents.preview', $document->id) }}" target="_blank" class="block w-full py-2 text-center text-sm font-semibold text-primary bg-primary/10 rounded-lg hover:bg-primary/20 transition-colors">
                                    Preview Dokumen
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-8 text-center text-gray-500 border-2 border-dashed border-gray-200 rounded-xl">
                        Belum ada lampiran dokumen pada laporan ini.
                    </div>
                @endforelse
            </div>
        </div>
    </x-ui.card>

    <!-- 4. Riwayat Verifikasi -->
    @if($report->reportVerifications->count() > 0)
    <x-ui.card>
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">4. Riwayat Verifikasi</h2>
        </div>
        <div class="p-6 bg-gray-50/30">
            <div class="space-y-4">
                @foreach($report->reportVerifications as $verification)
                <div class="bg-white rounded-xl p-4 md:p-5 border border-gray-200 shadow-sm">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            @php
                                $dec = strtolower($verification->decision);
                                $decBadge = 'bg-gray-100 text-gray-800';
                                if ($dec === 'disetujui') $decBadge = 'bg-green-100 text-green-800 border-green-200';
                                elseif ($dec === 'ditolak') $decBadge = 'bg-red-100 text-red-800 border-red-200';
                                elseif ($dec === 'perlu_perbaikan' || $dec === 'perlu perbaikan') $decBadge = 'bg-orange-100 text-orange-800 border-orange-200';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border {{ $decBadge }}">
                                {{ ucfirst(str_replace('_', ' ', $verification->decision)) }}
                            </span>
                            <p class="mt-2 text-sm text-gray-700 font-medium">{{ $verification->notes }}</p>
                        </div>
                        <div class="text-left md:text-right text-xs text-gray-500 flex flex-col gap-1">
                            <span class="font-semibold text-gray-700">{{ $verification->user?->full_name ?? '-' }}</span>
                            <span>{{ $verification->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>
    @endif

    <!-- 5. Form Keputusan -->
    @if($report->reportStatus->status_name === 'Pending' || $report->reportStatus->status_name === 'Diproses')
    <x-ui.card class="border-t-4 border-t-primary shadow-md">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-900">5. Form Keputusan Verifikasi</h2>
            <p class="text-sm text-gray-500 mt-1">Periksa dokumen dengan saksama sebelum mengambil keputusan.</p>
        </div>
        <div class="p-6">
            <form action="{{ route('sub_operator.laporan.verifikasi', $report->id) }}" method="POST" id="verificationForm">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Catatan Verifikasi (Wajib untuk Tolak / Perbaikan)</label>
                        <textarea name="notes" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-primary focus:border-primary sm:text-sm p-3" placeholder="Tuliskan alasan penolakan, detail perbaikan, atau catatan persetujuan..."></textarea>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-900 mb-4">Tentukan Tindakan</label>
                        
                        <div class="flex flex-col sm:flex-row gap-3">
                            <!-- Button Setujui (Green) -->
                            <button type="submit" name="decision" value="disetujui" class="flex-1 inline-flex justify-center items-center px-4 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors shadow-sm" onclick="return confirm('Apakah Anda yakin menyetujui laporan ini? Data akan diteruskan/dianggap valid.');">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Setujui Laporan
                            </button>

                            <!-- Button Perbaikan (Orange) -->
                            <button type="submit" name="decision" value="perlu_perbaikan" class="flex-1 inline-flex justify-center items-center px-4 py-3 bg-orange-500 text-white font-bold rounded-xl hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors shadow-sm" onclick="return confirm('Apakah Anda yakin meminta perbaikan? Laporan akan dikembalikan ke Pelapor.');">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                Minta Perbaikan
                            </button>

                            <!-- Button Tolak (Red) -->
                            <button type="submit" name="decision" value="ditolak" class="flex-1 inline-flex justify-center items-center px-4 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 transition-colors shadow-sm" onclick="return confirm('PERINGATAN: Apakah Anda yakin MENOLAK laporan ini? Keputusan ini bersifat final.');">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Tolak Laporan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </x-ui.card>
    @endif
</div>
@endsection
