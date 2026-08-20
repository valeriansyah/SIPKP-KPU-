@props([
    'variant' => 'primary', // primary, secondary, success, danger, warning, info
])

@php
    $baseClasses = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium';
    
    $variantClasses = [
        'primary' => 'bg-primary-100 text-primary-800',
        'secondary' => 'bg-gray-100 text-gray-800',
        'success' => 'bg-emerald-100 text-emerald-800',
        'danger' => 'bg-red-100 text-red-800',
        'warning' => 'bg-amber-100 text-amber-800',
        'info' => 'bg-blue-100 text-blue-800',
    ];
    
    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
