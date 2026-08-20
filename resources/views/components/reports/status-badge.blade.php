@props([
    'status' => 'Pending' // Pending, Diproses, Perlu Perbaikan, Disetujui, Ditolak
])

@php
    $config = [
        'Pending' => [
            'color' => 'bg-amber-100 text-amber-800 border-amber-200',
            'icon' => '<svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        ],
        'Diproses' => [
            'color' => 'bg-blue-100 text-blue-800 border-blue-200',
            'icon' => '<svg class="w-3.5 h-3.5 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>'
        ],
        'Perlu Perbaikan' => [
            'color' => 'bg-orange-100 text-orange-800 border-orange-200',
            'icon' => '<svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>'
        ],
        'Disetujui' => [
            'color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'icon' => '<svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        ],
        'Ditolak' => [
            'color' => 'bg-red-100 text-red-800 border-red-200',
            'icon' => '<svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        ]
    ];
    
    // Default to pending if unknown status
    $current = $config[$status] ?? $config['Pending'];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $current['color'] }}">
    {!! $current['icon'] !!}
    {{ $status }}
</span>
