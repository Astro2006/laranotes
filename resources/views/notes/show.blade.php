<x-layout :title="$note->title">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('notes.index')">
            {{ __('All notes') }}
        </flux:button>

        <flux:card class="mt-6 p-6 sm:p-8">
            <flux:heading size="xl">{{ $note->title }}</flux:heading>

            @if ($note->tags->isNotEmpty())
                <div class="mt-3 flex flex-wrap gap-1">
                    @foreach ($note->tags as $tag)
                        <x-tag-badge :tag="$tag" />
                    @endforeach
                </div>
            @endif

            <flux:text variant="subtle" class="mt-1 text-xs">
                {{ __('Created') }}
                <time datetime="{{ $note->created_at->toIso8601String() }}">
                    {{ $note->created_at->isoFormat('LLL') }}
                </time>

                @if ($note->updated_at->ne($note->created_at))
                    &middot; {{ __('edited') }}
                    <time datetime="{{ $note->updated_at->toIso8601String() }}">
                        {{ $note->updated_at->diffForHumans() }}
                    </time>
                @endif
            </flux:text>

            <div class="mt-6 whitespace-pre-line text-sm leading-relaxed text-gray-700 dark:text-zinc-300">
                {{ $note->content }}
            </div>
        </flux:card>

        <div class="mt-6 flex flex-wrap items-center gap-3">
            <flux:button variant="primary" icon="pencil" :href="route('notes.edit', $note)">{{ __('Edit note') }}</flux:button>

            <flux:modal.trigger name="delete-note">
                <flux:button variant="danger" icon="trash">{{ __('Delete note') }}</flux:button>
            </flux:modal.trigger>
        </div>

        <flux:modal name="delete-note" class="min-w-88">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Delete note?') }}</flux:heading>

                    <flux:text class="mt-2">
                        {{ __('You\'re about to delete ":title".', ['title' => $note->title]) }}<br>
                        {{ __('This action cannot be reversed.') }}
                    </flux:text>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />

                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <form method="POST" action="{{ route('notes.destroy', $note) }}">
                        @csrf
                        @method('DELETE')

                        <flux:button type="submit" variant="danger">{{ __('Delete note') }}</flux:button>
                    </form>
                </div>
            </div>
        </flux:modal>
    </main>
</x-layout>
