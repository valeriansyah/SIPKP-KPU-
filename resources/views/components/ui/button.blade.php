@props([
    'variant' => 'primary', // primary, secondary, outline, danger, warning
    'size' => 'md', // sm, md, lg
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed';
    
    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs shadow-sm',
        'md' => 'px-4 py-2 text-sm shadow-sm',
        'lg' => 'px-6 py-3 text-base shadow-md',
    ];
    
    $variantClasses = [
        'primary' => 'bg-primary text-white hover:bg-primary-dark hover:shadow-md focus:ring-primary',
        'secondary' => 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50 hover:border-gray-300 focus:ring-primary',
        'outline' => 'bg-transparent text-primary border border-primary hover:bg-primary-50 focus:ring-primary',
        'danger' => 'bg-danger text-white hover:bg-red-700 hover:shadow-md focus:ring-danger',
        'warning' => 'bg-warning text-white hover:bg-amber-600 hover:shadow-md focus:ring-warning',
    ];
    
    $classes = $baseClasses . ' ' . $sizeClasses[$size] . ' ' . $variantClasses[$variant];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
