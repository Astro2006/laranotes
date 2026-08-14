@props(['note'])

@php
    $accents = [
        'bg-pink-600',
        'bg-purple-600',
        'bg-yellow-500',
        'bg-green-500',
        'bg-sky-600',
        'bg-indigo-600',
    ];

    $accent = $accents[$note->id % count($accents)];

    $initials = Str::of($note->title)
        ->squish()
        ->explode(' ')
        ->take(2)
        ->map(fn (string $word): string => Str::upper(Str::substr($word, 0, 1)))
        ->implode('');
@endphp

<li class="col-span-1 flex rounded-md shadow-xs">
    <div class="{{ $accent }} flex w-16 shrink-0 items-center justify-center rounded-l-md text-sm font-medium text-white">
        {{ $initials }}
    </div>

    <div class="flex flex-1 items-center justify-between truncate rounded-r-md border-t border-r border-b border-gray-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex-1 truncate px-4 py-2 text-sm">
            <a
                href="{{ route('notes.show', $note) }}"
                class="font-medium text-gray-900 hover:text-gray-600 dark:text-zinc-100 dark:hover:text-zinc-300"
            >
                {{ $note->title }}
            </a>

            <p class="truncate text-gray-500 dark:text-zinc-400">{{ $note->content }}</p>
        </div>

        <div class="shrink-0 px-3">
            <time
                datetime="{{ $note->created_at->toIso8601String() }}"
                class="text-xs text-gray-400 dark:text-zinc-500"
            >
                {{ $note->created_at->diffForHumans(short: true) }}
            </time>
        </div>
    </div>
</li>
