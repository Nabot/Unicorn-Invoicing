@props(['name', 'size' => 'md'])

@php
    $initials = strtoupper(substr($name, 0, 2));
    $colors = [
        'bg-brand-gold', 'bg-brand-gold-dark', 'bg-yellow-500', 'bg-purple-500', 
        'bg-pink-500', 'bg-indigo-500', 'bg-red-500', 'bg-teal-500'
    ];
    $colorIndex = crc32($name) % count($colors);
    $color = $colors[$colorIndex];
    
    $sizes = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="flex-shrink-0 {{ $sizeClass }} rounded-full {{ $color }} {{ str_contains($color, 'brand-gold') ? 'text-brand-black' : 'text-white' }} flex items-center justify-center font-semibold">
    {{ $initials }}
</div>
