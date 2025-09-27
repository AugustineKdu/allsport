@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 font-semibold rounded-lg transition-all duration-200 hover:bg-indigo-200'
            : 'inline-flex items-center px-4 py-2 text-gray-600 font-medium rounded-lg transition-all duration-200 hover:bg-gray-100 hover:text-gray-800';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
