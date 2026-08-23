@props(['title', 'value', 'status' => 'default'])

@php
    // Define semantic colors based on status
    $colors = [
        'default' => [
            'bg' => 'bg-white',
            'border' => 'border-gray-200',
            'iconBg' => 'bg-gray-100',
            'iconText' => 'text-gray-500',
            'title' => 'text-gray-500',
            'value' => 'text-gray-900',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'
        ],
        'total' => [
            'bg' => 'bg-white',
            'border' => 'border-red-800',
            'iconBg' => 'bg-red-50',
            'iconText' => 'text-red-800',
            'title' => 'text-gray-500',
            'value' => 'text-gray-900',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>'
        ],
        'pending' => [
            'bg' => 'bg-white',
            'border' => 'border-amber-500',
            'iconBg' => 'bg-amber-50',
            'iconText' => 'text-amber-500',
            'title' => 'text-gray-500',
            'value' => 'text-gray-900',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ],
        'diproses' => [
            'bg' => 'bg-white',
            'border' => 'border-blue-500',
            'iconBg' => 'bg-blue-50',
            'iconText' => 'text-blue-500',
            'title' => 'text-gray-500',
            'value' => 'text-gray-900',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>'
        ],
        'perlu_perbaikan' => [
            'bg' => 'bg-white',
            'border' => 'border-orange-500',
            'iconBg' => 'bg-orange-50',
            'iconText' => 'text-orange-500',
            'title' => 'text-gray-500',
            'value' => 'text-gray-900',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>'
        ],
        'disetujui' => [
            'bg' => 'bg-white',
            'border' => 'border-green-500',
            'iconBg' => 'bg-green-50',
            'iconText' => 'text-green-500',
            'title' => 'text-gray-500',
            'value' => 'text-gray-900',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ],
        'ditolak' => [
            'bg' => 'bg-white',
            'border' => 'border-red-500',
            'iconBg' => 'bg-red-50',
            'iconText' => 'text-red-500',
            'title' => 'text-gray-500',
            'value' => 'text-gray-900',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ],
    ];

    $style = $colors[$status] ?? $colors['default'];
@endphp

<div class="{{ $style['bg'] }} rounded-xl border-l-4 {{ $style['border'] }} p-5 shadow-sm hover:shadow-md transition-shadow duration-200 border-t border-r border-b border-t-gray-100 border-r-gray-100 border-b-gray-100 flex items-center justify-between">
    <div>
        <h3 class="text-xs font-bold {{ $style['title'] }} uppercase tracking-wider mb-1">{{ $title }}</h3>
        <p class="text-3xl font-extrabold {{ $style['value'] }}">{{ $value }}</p>
    </div>
    <div class="w-12 h-12 rounded-full {{ $style['iconBg'] }} flex items-center justify-center">
        <svg class="w-6 h-6 {{ $style['iconText'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $style['icon'] !!}
        </svg>
    </div>
</div>
