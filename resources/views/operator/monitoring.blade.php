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
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
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
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-base font-semibold text-text">Laporan Terbaru Masuk</h2>
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
                        @foreach($reports->sortByDesc('created_at')->take(5) as $report)
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
                                    <a href="{{ route('operator.laporan.show', $report->id) }}" class="text-primary hover:text-primary-dark">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                        @if($reports->isEmpty())
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Belum ada laporan di seluruh wilayah.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
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
