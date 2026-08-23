@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-text">Detail & Verifikasi Laporan</h1>
            <p class="text-sm text-muted mt-1">Rincian laporan {{ $report->report_number }}</p>
        </div>
        <a href="{{ route('sub_operator.antrean') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded hover:bg-gray-50 transition-colors text-sm font-medium">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Verifikasi Gagal!</strong>
            <ul class="list-disc pl-5 mt-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-surface rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-semibold text-text">Informasi Laporan</h2>
                <p class="text-sm text-muted">Dibuat pada {{ $report->created_at->format('d F Y, H:i') }}</p>
            </div>
            @php
                $statusClass = 'bg-gray-100 text-gray-800';
                if($report->reportStatus->status_name === 'Pending') $statusClass = 'bg-yellow-100 text-yellow-800';
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
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Almarhum</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">NIK</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $report->deceased->nik }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
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
                        <dd class="mt-1 text-sm text-gray-900">{{ $report->deceased->district->name }}</dd>
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

    @if($report->reportStatus->status_name === 'Pending' || $report->reportStatus->status_name === 'Diproses')
    <div class="bg-surface rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-text">Form Verifikasi</h2>
        </div>
        <div class="p-6">
            <form action="{{ route('sub_operator.laporan.verifikasi', $report->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-text mb-1">Keputusan</label>
                        <select name="decision" class="w-full rounded border border-gray-300 p-2 text-sm">
                            <option value="disetujui">Setujui</option>
                            <option value="ditolak">Tolak</option>
                            <option value="perlu_perbaikan">Perlu Perbaikan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text mb-1">Catatan</label>
                        <textarea name="notes" rows="3" class="w-full rounded border border-gray-300 p-2 text-sm"></textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark text-sm font-medium">
                        Simpan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
