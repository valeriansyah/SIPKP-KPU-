@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-text">Monitoring Seluruh Laporan</h1>
            <p class="text-sm text-muted mt-1">Pantau seluruh laporan kematian dari semua wilayah kerja Provinsi Sumatera Selatan.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-yellow-200 p-4 shadow-sm bg-yellow-50/30">
            <h3 class="text-xs font-semibold text-yellow-700 uppercase tracking-wider">Pending</h3>
            <p class="text-2xl font-bold text-yellow-900 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-blue-200 p-4 shadow-sm bg-blue-50/30">
            <h3 class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Diproses</h3>
            <p class="text-2xl font-bold text-blue-900 mt-1">{{ $stats['diproses'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-green-200 p-4 shadow-sm bg-green-50/30">
            <h3 class="text-xs font-semibold text-green-700 uppercase tracking-wider">Disetujui</h3>
            <p class="text-2xl font-bold text-green-900 mt-1">{{ $stats['disetujui'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-red-200 p-4 shadow-sm bg-red-50/30">
            <h3 class="text-xs font-semibold text-red-700 uppercase tracking-wider">Ditolak</h3>
            <p class="text-2xl font-bold text-red-900 mt-1">{{ $stats['ditolak'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-orange-200 p-4 shadow-sm bg-orange-50/30">
            <h3 class="text-xs font-semibold text-orange-700 uppercase tracking-wider">Perbaikan</h3>
            <p class="text-2xl font-bold text-orange-900 mt-1">{{ $stats['perbaikan'] }}</p>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart -->
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm col-span-1">
            <div class="p-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-text">Persentase Status</h2>
            </div>
            <div class="p-5 flex justify-center items-center">
                <div class="relative w-full max-w-[250px] aspect-square">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Reports Table -->
        <div class="bg-surface rounded-lg border border-gray-200 shadow-sm col-span-1 lg:col-span-2">
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-base font-semibold text-text">Daftar Laporan Kematian</h2>
                
                <!-- Filter Form -->
                <form action="{{ route('operator.monitoring') }}" method="GET" class="w-full sm:w-auto">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor/nama/NIK..." class="w-full text-sm px-3 py-2 pl-9 border border-gray-300 rounded-md focus:ring-primary focus:border-primary">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <div>
                            <select name="district_id" class="w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-white">
                                <option value="">Semua Wilayah</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="status" class="w-full text-sm px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary bg-white">
                                <option value="">Semua Status</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="Diproses" {{ request('status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                                <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="Perlu Perbaikan" {{ request('status') == 'Perlu Perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                            </select>
                        </div>
                        <button type="submit" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-md hover:bg-primary-dark transition-colors">Filter</button>
                        @if(request()->anyFilled(['search', 'district_id', 'status']))
                            <a href="{{ route('operator.monitoring') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors text-center">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Laporan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kab/Kota</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reports as $report)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $report->report_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $report->deceased->district->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                        if($report->reportStatus->status_name === 'Pending') $statusClass = 'bg-yellow-100 text-yellow-800';
                                        if($report->reportStatus->status_name === 'Diproses') $statusClass = 'bg-blue-100 text-blue-800';
                                        if($report->reportStatus->status_name === 'Disetujui') $statusClass = 'bg-green-100 text-green-800';
                                        if($report->reportStatus->status_name === 'Ditolak') $statusClass = 'bg-red-100 text-red-800';
                                        if($report->reportStatus->status_name === 'Perlu Perbaikan') $statusClass = 'bg-orange-100 text-orange-800';
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $report->reportStatus->status_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('operator.laporan.show', $report->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-primary bg-primary-light/20 hover:bg-primary-light/40 transition-colors">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @if($reports->isEmpty())
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Tidak ada laporan ditemukan.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if($reports->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $reports->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('statusChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Diproses', 'Disetujui', 'Ditolak', 'Perlu Perbaikan'],
                    datasets: [{
                        data: [
                            {{ $stats['pending'] }},
                            {{ $stats['diproses'] }},
                            {{ $stats['disetujui'] }},
                            {{ $stats['ditolak'] }},
                            {{ $stats['perbaikan'] }}
                        ],
                        backgroundColor: [
                            '#FCD34D', // Yellow 300
                            '#93C5FD', // Blue 300
                            '#6EE7B7', // Green 300
                            '#FCA5A5', // Red 300
                            '#FDBA74'  // Orange 300
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endpush
@endsection
