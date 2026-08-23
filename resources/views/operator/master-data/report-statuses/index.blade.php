@extends('layouts.app')

@section('title', 'Status Laporan')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-text">Status Laporan</h1>
        <p class="text-text-secondary mt-1">Sesuaikan deskripsi status alur sistem pelaporan.</p>
    </div>
    <div class="bg-yellow-50 text-yellow-800 text-xs px-3 py-1.5 rounded border border-yellow-200 flex items-center">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Status Key (Nama) dilindungi sistem. Hanya deskripsi yang dapat diubah.
    </div>
</div>

@if (session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
@endif

<x-ui.card class="bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider">Status Key</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider">Deskripsi Tampil</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Jml Laporan</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reportStatuses as $status)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-bold text-text font-mono text-sm px-2 py-1 bg-gray-100 rounded inline-block">{{ $status->status_name }}</div>
                    </td>
                    <td class="py-3 px-4 text-sm text-text-secondary max-w-sm">
                        {{ $status->description ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-sm text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $status->reports_count }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <a href="{{ route('operator.master-data.report-statuses.edit', $status->id) }}" class="text-primary hover:text-primary-dark p-1 inline-block" title="Edit Deskripsi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center text-text-secondary">
                        Belum ada data Status Laporan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-ui.card>
@endsection
