@props(['note'])

<li>
    <flux:card class="flex items-center gap-4 p-4">
        <flux:avatar name="{{ $note->title }}" color="auto" color:seed="{{ $note->id }}" />

        <div class="min-w-0 flex-1">
            <a
                href="{{ route('notes.show', $note) }}"
                class="font-medium text-gray-900 hover:text-gray-600 dark:text-zinc-100 dark:hover:text-zinc-300"
            >
                {{ $note->title }}
            </a>

            <p class="truncate text-gray-500 dark:text-zinc-400">{{ $note->content }}</p>

            @if ($note->tags->isNotEmpty())
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach ($note->tags as $tag)
                        <flux:badge size="sm">{{ $tag->name }}</flux:badge>
                    @endforeach
                </div>
            @endif
        </div>

        <time
            datetime="{{ $note->created_at->toIso8601String() }}"
            class="shrink-0 text-xs text-gray-400 dark:text-zinc-500"
        >
            {{ $note->created_at->diffForHumans(short: true) }}
        </time>
    </flux:card>
</li>
