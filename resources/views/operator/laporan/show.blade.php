@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-text">Detail Laporan</h1>
            <p class="text-sm text-muted mt-1">Rincian laporan {{ $report->report_number }} (Hanya Baca)</p>
        </div>
        <a href="{{ route('operator.monitoring') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded hover:bg-gray-50 transition-colors text-sm font-medium">
            Kembali ke Monitoring
        </a>
    </div>

    <div class="bg-surface rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-text">Informasi Laporan</h2>
                <p class="text-sm text-muted">Dibuat pada {{ $report->created_at->format('d F Y, H:i') }}</p>
            </div>
            @php
                $statusClass = 'bg-gray-100 text-gray-800';
                if($report->reportStatus->status_name === 'Pending') $statusClass = 'bg-yellow-100 text-yellow-800';
                if($report->reportStatus->status_name === 'Diproses') $statusClass = 'bg-blue-100 text-blue-800';
                if($report->reportStatus->status_name === 'Disetujui') $statusClass = 'bg-green-100 text-green-800';
                if($report->reportStatus->status_name === 'Ditolak') $statusClass = 'bg-red-100 text-red-800';
                if($report->reportStatus->status_name === 'Perlu Perbaikan') $statusClass = 'bg-orange-100 text-orange-800';
            @endphp
            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $statusClass }}">
                {{ $report->reportStatus->status_name }}
            </span>
        </div>
        
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Pelapor & Almarhum</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Pelapor</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $report->user->full_name }}</dd>
                    </div>
                    <div class="border-t border-gray-100 pt-3">
                        <dt class="text-sm font-medium text-gray-500">NIK Almarhum</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $report->deceased->nik }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Lengkap Almarhum</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $report->deceased->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Jenis Kelamin</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $report->deceased->gender }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tempat, Tanggal Lahir</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $report->deceased->birth_place }}, {{ \Carbon\Carbon::parse($report->deceased->birth_date)->format('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tanggal Meninggal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($report->deceased->death_date)->format('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kabupaten/Kota</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $report->deceased->district->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            
            <div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Lampiran Dokumen</h3>
                <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                    @forelse($report->documents as $document)
                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                            <div class="w-0 flex-1 flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 flex-1 w-0 truncate">
                                    {{ $document->documentType->name }}
                                </span>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                @if($document->is_dummy)
                                    <span class="text-sm text-gray-400 italic">File demo tidak tersedia</span>
                                @else
                                    <a href="{{ route('documents.preview', $document->id) }}" target="_blank" class="font-medium text-primary hover:text-primary-dark">
                                        Lihat
                                    </a>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="pl-3 pr-4 py-3 text-sm text-gray-500">Belum ada lampiran dokumen pada laporan ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    @if($report->reportVerifications->count() > 0)
    <div class="bg-surface rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-text">Riwayat Verifikasi</h2>
        </div>
        <div class="p-6">
            <ul class="space-y-4">
                @foreach($report->reportVerifications as $verification)
                <li class="bg-gray-50 rounded-md p-4 border border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $verification->decision === 'disetujui' ? 'bg-green-100 text-green-800' : 
                                  ($verification->decision === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800') }}">
                                {{ ucfirst(str_replace('_', ' ', $verification->decision)) }}
                            </span>
                            <p class="mt-2 text-sm text-gray-700">{{ $verification->notes }}</p>
                        </div>
                        <div class="text-right text-xs text-gray-500">
                            <p>{{ $verification->created_at->format('d M Y, H:i') }}</p>
                            <p class="mt-1">Oleh: {{ $verification->user?->full_name ?? '-' }}</p>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div>
@endsection
