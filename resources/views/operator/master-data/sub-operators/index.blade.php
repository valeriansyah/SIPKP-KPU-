@extends('layouts.app')

@section('title', 'Manajemen Sub Operator')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-text">Sub Operator</h1>
        <p class="text-text-secondary mt-1">Kelola akun verifikator tiap Kabupaten/Kota.</p>
    </div>
    <a href="{{ route('operator.master-data.sub-operators.create') }}">
        <x-ui.button variant="primary" class="h-10 px-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Tambah Akun
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
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider">Nama Lengkap</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider">Email</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider">Wilayah (District)</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider text-center">Status</th>
                    <th class="py-3 px-4 text-xs font-semibold text-text-secondary uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($subOperators as $user)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-3 px-4">
                        <div class="font-medium text-text flex items-center">
                            @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" class="w-8 h-8 rounded-full object-cover mr-3 bg-gray-200" alt="Avatar">
                            @else
                                <div class="w-8 h-8 rounded-full bg-primary-light text-primary flex items-center justify-center mr-3 text-xs font-bold">
                                    {{ substr($user->full_name, 0, 2) }}
                                </div>
                            @endif
                            {{ $user->full_name }}
                        </div>
                    </td>
                    <td class="py-3 px-4 text-sm text-text-secondary">
                        {{ $user->email }}
                    </td>
                    <td class="py-3 px-4 text-sm text-text-secondary">
                        @if($user->district)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $user->district->name }}
                            </span>
                        @else
                            <span class="text-red-500 italic">Belum diset</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-sm text-center">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-right">
                        <a href="{{ route('operator.master-data.sub-operators.edit', $user->id) }}" class="text-primary hover:text-primary-dark p-1 inline-block" title="Edit Akun">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-text-secondary">
                        Belum ada data Sub Operator.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($subOperators->hasPages())
    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        {{ $subOperators->links() }}
    </div>
    @endif
</x-ui.card>
@endsection
