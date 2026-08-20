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

    <div class="bg-surface rounded border border-gray-200 overflow-hidden">
        <div class="p-6">
            <p class="text-sm text-muted">Halaman Verifikasi (Dalam Pengembangan Phase 6E & 6G)</p>
            
            @if ($errors->any())
                <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Verifikasi Gagal!</strong>
                    <ul class="list-disc pl-5 mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('sub_operator.laporan.verifikasi', $report->id) }}" method="POST" class="mt-4">
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
</div>
@endsection
