@props([
    'variant' => 'primary', // primary, secondary, success, danger, warning, info
])

@php
    $baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide border shadow-sm';
    
    $variantClasses = [
        'primary' => 'bg-red-50 text-red-700 border-red-200',
        'secondary' => 'bg-gray-50 text-gray-700 border-gray-200',
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'danger' => 'bg-red-50 text-red-700 border-red-200',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
        'info' => 'bg-blue-50 text-blue-700 border-blue-200',
    ];
    
    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
