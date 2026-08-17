@props(['tag'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400']) }}>
    {{ $tag->name }}
</span>
