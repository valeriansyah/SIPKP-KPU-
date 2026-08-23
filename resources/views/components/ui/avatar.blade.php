@props([
    'user',
    'size' => 'md',
    'showRole' => false,
])

@php
    $sizeClasses = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-lg',
        'xl' => 'w-24 h-24 text-3xl',
    ][$size] ?? 'w-10 h-10 text-sm';

    $roleName = $user->role ? strtolower($user->role->role_name) : '';
    $roleColor = 'bg-gray-500';
    $roleBorder = 'border-gray-500';
    $roleText = 'text-gray-600';
    
    if (str_contains($roleName, 'pelapor')) {
        $roleColor = 'bg-red-500';
        $roleBorder = 'border-red-500';
        $roleText = 'text-red-600';
    } elseif (str_contains($roleName, 'sub operator')) {
        $roleColor = 'bg-green-500';
        $roleBorder = 'border-green-500';
        $roleText = 'text-green-600';
    } elseif (str_contains($roleName, 'operator')) {
        $roleColor = 'bg-blue-500';
        $roleBorder = 'border-blue-500';
        $roleText = 'text-blue-600';
    }

    $initial = strtoupper(substr($user->full_name ?? 'U', 0, 1));
    $hasPhoto = !empty($user->profile_photo_url);
@endphp

<div {{ $attributes->merge(['class' => "relative rounded-full flex items-center justify-center shrink-0 shadow-sm border border-gray-200 bg-gray-100 $sizeClasses"]) }}>
    <!-- Image Avatar -->
    <img src="{{ $hasPhoto ? $user->profile_photo_url : '' }}" 
         alt="{{ $user->full_name }}" 
         class="avatar-image w-full h-full rounded-full object-cover {{ $hasPhoto ? '' : 'hidden' }}">
    
    <!-- Initial Fallback (Icon) -->
    <span class="avatar-initial {{ $roleText }} {{ $hasPhoto ? 'hidden' : 'flex' }} items-center justify-center w-full h-full opacity-60">
        <svg class="w-1/2 h-1/2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
    </span>

    <!-- Role Indicator -->
    @if($showRole)
        <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white {{ $roleColor }}" title="{{ $user->role->role_name ?? 'Role' }}"></span>
    @endif
</div>
