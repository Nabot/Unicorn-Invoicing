@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-gold text-start text-base font-medium text-brand-gold bg-brand-black/50 focus:outline-none focus:text-brand-gold-light focus:bg-brand-black focus:border-brand-gold-light transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-300 hover:text-brand-gold hover:bg-brand-black/30 hover:border-brand-gold focus:outline-none focus:text-brand-gold focus:bg-brand-black/30 focus:border-brand-gold transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
