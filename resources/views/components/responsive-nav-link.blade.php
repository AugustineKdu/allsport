@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-3 text-start text-base font-semibold text-indigo-700 bg-indigo-50 rounded-xl mx-2 mb-1 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200'
            : 'block w-full px-4 py-3 text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-xl mx-2 mb-1 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
