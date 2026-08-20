<div {{ $attributes->merge(['class' => 'bg-surface rounded-md shadow-sm border border-border overflow-hidden']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-border bg-gray-50/50 flex items-center justify-between">
            {{ $header }}
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-border bg-gray-50/50">
            {{ $footer }}
        </div>
    @endif
</div>
