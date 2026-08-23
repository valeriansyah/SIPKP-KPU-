@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-text">Daftar Laporan</h1>
            <p class="text-sm text-muted mt-1">Kelola laporan yang telah Anda buat.</p>
        </div>
        <a href="{{ route('pelapor.laporan.create') }}" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition-colors text-sm font-medium">
            + Buat Laporan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-surface rounded border border-gray-200 overflow-hidden shadow-sm">
        @if($reports->isEmpty())
            <div class="p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada laporan</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum membuat laporan kematian pemilih satupun.</p>
                <div class="mt-6">
                    <a href="{{ route('pelapor.laporan.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Buat Laporan
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Laporan / Tanggal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Almarhum</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reports as $report)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-extrabold text-gray-900 tracking-wide">{{ $report->report_number }}</div>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $report->created_at->format('d M Y, H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $report->deceased->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1 bg-gray-100 inline-block px-2 py-0.5 rounded border border-gray-200">NIK: {{ $report->deceased->nik }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusName = $report->reportStatus->status_name ?? '';
                                        $statusClass = 'bg-gray-100 text-gray-800 border-gray-200';
                                        
                                        if(in_array($statusName, ['Pending', 'Diajukan', 'Perlu Perbaikan'])) {
                                            $statusClass = 'bg-orange-50 text-orange-700 border-orange-200';
                                        } elseif(in_array($statusName, ['Diproses', 'Diverifikasi'])) {
                                            $statusClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                        } elseif($statusName === 'Disetujui') {
                                            $statusClass = 'bg-green-50 text-green-700 border-green-200';
                                        } elseif($statusName === 'Ditolak') {
                                            $statusClass = 'bg-red-50 text-red-700 border-red-200';
                                        }
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $statusClass }}">
                                        {{ $statusName }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('pelapor.laporan.show', $report->id) }}" class="inline-flex items-center px-4 py-1.5 border border-gray-300 shadow-sm text-xs font-bold rounded-full text-gray-700 bg-white hover:bg-gray-50 hover:text-red-700 hover:border-red-300 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Lihat Detail
                                        <svg class="ml-1.5 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
