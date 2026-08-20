@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-text">Detail Laporan</h1>
            <p class="text-sm text-muted mt-1">Rincian laporan {{ $report->report_number }}</p>
        </div>
        <a href="{{ route('operator.monitoring') }}" class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded hover:bg-gray-50 transition-colors text-sm font-medium">
            Kembali
        </a>
    </div>

    <div class="bg-surface rounded border border-gray-200 overflow-hidden">
        <div class="p-6">
            <p class="text-sm text-muted">Halaman Read-Only Monitoring Laporan (Dalam Pengembangan Phase 6I)</p>
        </div>
    </div>
</div>
@endsection
