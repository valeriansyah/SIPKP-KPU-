@extends('layouts.app')

@section('title', 'Kabupaten/Kota')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-text">Kabupaten/Kota</h1>
        <p class="text-text-secondary mt-1">Kelola data wilayah operasional pelaporan.</p>
    </div>
    <a href="{{ route('operator.master-data.districts.create') }}">
        <x-ui.button variant="primary" class="h-10 px-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Kabupaten/Kota
        </x-ui.button>
    </a>
</div>

@if (session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
@endif
@if (session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
@endif

<x-ui.card class="bg-white border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider">Nama Kabupaten/Kota</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider">Kode</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Jml Akun</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Jml Laporan</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($districts as $district)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-medium text-text">{{ $district->name }}</div>
                    </td>
                    <td class="py-3 px-4 text-sm text-text-secondary">
                        {{ $district->code ?? '-' }}
                    </td>
                    <td class="py-3 px-4 text-sm text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $district->users_count }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $district->deceased_count }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('operator.master-data.districts.edit', $district->id) }}" class="text-primary hover:text-primary-dark p-1" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('operator.master-data.districts.destroy', $district->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="inline-block">
                                @csrf
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-text-secondary">
                        Belum ada data Kabupaten/Kota.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($districts->hasPages())
    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        {{ $districts->links() }}
    </div>
    @endif
</x-ui.card>
@endsection
