<x-layout :title="$note->title">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('notes.index')">
            All notes
        </flux:button>

        <flux:card class="mt-6 p-6 sm:p-8">
            <flux:heading size="xl">{{ $note->title }}</flux:heading>

            <flux:text variant="subtle" class="mt-1 text-xs">
                Created
                <time datetime="{{ $note->created_at->toIso8601String() }}">
                    {{ $note->created_at->isoFormat('LLL') }}
                </time>

                @if ($note->updated_at->ne($note->created_at))
                    &middot; edited
                    <time datetime="{{ $note->updated_at->toIso8601String() }}">
                        {{ $note->updated_at->diffForHumans() }}
                    </time>
                @endif
            </flux:text>

            <div class="mt-6 whitespace-pre-line text-sm leading-relaxed text-gray-700 dark:text-zinc-300">
                {{ $note->content }}
            </div>

            @if ($note->tags->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($note->tags as $tag)
                        <flux:badge size="sm">{{ $tag->name }}</flux:badge>
                    @endforeach
                </div>
            @endif
        </flux:card>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <flux:button variant="primary" icon="pencil" :href="route('notes.edit', $note)">Edit note</flux:button>

            <form method="POST" action="{{ route('notes.destroy', $note) }}">
                @csrf
                @method('DELETE')

                <flux:button type="submit" variant="danger" icon="trash">Delete note</flux:button>
            </form>
        </div>
    </main>
</x-layout>
