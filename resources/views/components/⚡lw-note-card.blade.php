<?php

use App\Models\Notes;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public Notes $note;
};
?>

<li>
    <flux:card class="flex items-center gap-4 p-4">
        <flux:avatar name="{{ $note->title }}" color="auto" color:seed="{{ $note->id }}" />

        <div class="min-w-0 flex-1">
            <a
                href="{{ route('lw.notes.show', $note) }}"
                wire:navigate
                class="font-medium text-gray-900 hover:text-gray-600 dark:text-zinc-100 dark:hover:text-zinc-300"
            >
                {{ $note->title }}
            </a>

            <p class="truncate text-gray-500 dark:text-zinc-400">{{ Str::of($note->content)->stripTags()->squish() }}</p>

            @if ($note->tags->isNotEmpty())
                <div class="mt-1 flex flex-wrap gap-1">
                    @foreach ($note->tags as $tag)
                        <x-tag-badge :tag="$tag" />
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
