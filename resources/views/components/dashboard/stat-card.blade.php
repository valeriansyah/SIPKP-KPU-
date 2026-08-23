@props(['title', 'value', 'status' => 'default', 'description' => null, 'variant' => 'outline'])

@php
    $isSolid = $variant === 'solid';

    $colors = [
        'default' => [
            'border' => 'border-gray-300', 'title' => 'text-gray-500', 'value' => 'text-gray-900', 'solidBg' => 'bg-gray-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>'
        ],
        'total' => [
            'border' => 'border-red-600', 'title' => 'text-gray-600', 'value' => 'text-gray-900', 'solidBg' => 'bg-red-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>'
        ],
        'pending' => [
            'border' => 'border-amber-400', 'title' => 'text-gray-600', 'value' => 'text-gray-900', 'solidBg' => 'bg-amber-500',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ],
        'diproses' => [
            'border' => 'border-blue-500', 'title' => 'text-gray-600', 'value' => 'text-gray-900', 'solidBg' => 'bg-blue-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>'
        ],
        'perlu_perbaikan' => [
            'border' => 'border-orange-500', 'title' => 'text-gray-600', 'value' => 'text-gray-900', 'solidBg' => 'bg-orange-500',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>'
        ],
        'disetujui' => [
            'border' => 'border-green-500', 'title' => 'text-gray-600', 'value' => 'text-gray-900', 'solidBg' => 'bg-green-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ],
        'ditolak' => [
            'border' => 'border-red-500', 'title' => 'text-gray-600', 'value' => 'text-gray-900', 'solidBg' => 'bg-red-700',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        ],
    ];

    $cfg = $colors[$status] ?? $colors['default'];
    
    $containerClass = $isSolid ? "{$cfg['solidBg']} text-white border-transparent" : "bg-white border-gray-200 border-l-4 {$cfg['border']}";
    $titleClass = $isSolid ? "text-white/80" : $cfg['title'];
    $valueClass = $isSolid ? "text-white" : $cfg['value'];
    $descClass = $isSolid ? "text-white/80" : "text-gray-500";
    $borderTopClass = $isSolid ? "border-white/20" : "border-gray-100";
    $iconOpacity = $isSolid ? "opacity-20" : "opacity-5";
    $iconColor = $isSolid ? "text-white" : "text-gray-900";
    $hoverClass = $isSolid ? "hover:-translate-y-1 hover:shadow-lg" : "hover:shadow-md";
    $iconPosClass = $isSolid ? "right-[-15px] bottom-[-15px]" : "right-[-10px] top-[-10px]";
@endphp

<div class="group h-full rounded-xl border {{ $containerClass }} p-6 shadow-sm transition-all duration-300 {{ $hoverClass }} relative overflow-hidden flex flex-col justify-between">
    <!-- Icon in Background -->
    <div class="absolute {{ $iconPosClass }} {{ $iconOpacity }} {{ $iconColor }} pointer-events-none transition-transform duration-300 group-hover:scale-110">
        <svg class="w-28 h-28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $cfg['icon'] !!}
        </svg>
    </div>
    
    <div class="relative z-10">
        <h3 class="text-xs font-bold {{ $titleClass }} uppercase tracking-wider mb-4">{{ $title }}</h3>
        <p class="text-4xl font-black {{ $valueClass }}">{{ $value }}</p>
    </div>
    
    @if($description)
    <div class="mt-4 pt-4 border-t {{ $borderTopClass }} relative z-10">
        <p class="text-sm font-medium {{ $descClass }}">{{ $description }}</p>
    </div>
    @endif
</div>
