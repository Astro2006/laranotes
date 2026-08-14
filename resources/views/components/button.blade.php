@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'lg',
])

@php
    $sizes = [
        'xs' => 'rounded-sm px-2 py-1 text-xs',
        'sm' => 'rounded-sm px-2 py-1 text-sm',
        'md' => 'rounded-md px-2.5 py-1.5 text-sm',
        'lg' => 'rounded-md px-3 py-2 text-sm',
        'xl' => 'rounded-md px-3.5 py-2.5 text-sm',
    ];

    $variants = [
        'primary' => 'bg-indigo-600 text-white hover:bg-indigo-500 focus-visible:outline-indigo-600',
        'secondary' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus-visible:outline-indigo-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800',
        'soft' => 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100 focus-visible:outline-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 dark:hover:bg-indigo-500/20',
        'danger' => 'bg-red-600 text-white hover:bg-red-500 focus-visible:outline-red-600',
    ];

    $classes = 'inline-flex items-center justify-center gap-x-1.5 font-semibold shadow-xs transition focus-visible:outline-2 focus-visible:outline-offset-2 '
        . ($sizes[$size] ?? $sizes['lg']) . ' '
        . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $icon ?? '' }}{{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
        {{ $icon ?? '' }}{{ $slot }}
    </button>
@endif
