<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.04)] border border-gray-100 overflow-hidden']) }}>
    @if(isset($header))
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
            {{ $header }}
        </div>
    @endif
    
    <div class="p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
            {{ $footer }}
        </div>
    @endif
</div>
