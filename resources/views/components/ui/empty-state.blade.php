<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-8 md:p-12 text-center bg-gray-50/50 rounded-md border border-dashed border-gray-300']) }}>
    @if(isset($icon))
        <div class="w-16 h-16 mb-4 text-gray-400 bg-gray-100 rounded-full flex items-center justify-center">
            {{ $icon }}
        </div>
    @else
        <div class="w-16 h-16 mb-4 text-gray-400 bg-gray-100 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
        </div>
    @endif

    <h3 class="text-lg font-semibold text-text">{{ $title ?? 'Data Tidak Ditemukan' }}</h3>
    
    @if(isset($description))
        <p class="text-sm text-text-secondary mt-1 max-w-sm">{{ $description }}</p>
    @endif

    @if(isset($action))
        <div class="mt-6">
            {{ $action }}
        </div>
    @endif
</div>
