@props(['tag'])

<flux:badge size="sm" color="{{ $tag->badge_color }}" {{ $attributes }}>
    {{ $tag->name }}
</flux:badge>
