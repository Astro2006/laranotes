<x-layout :title="__('Notes')">
    <main class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 sm:py-14">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <flux:heading size="lg">
                @if ($search)
                    {{ trans_choice('{0}No notes found|{1}1 note found|[2,*]:count notes found', $notes->total(), ['count' => $notes->total()]) }}
                    {{ __('for ":search"', ['search' => $search]) }}
                @else
                    {{ trans_choice('{0}No notes|{1}1 note|[2,*]:count notes', $notes->total(), ['count' => $notes->total()]) }}
                @endif
            </flux:heading>

            <flux:button variant="primary" icon="plus" :href="route('notes.create')">{{ __('New note') }}</flux:button>
        </div>

        <form method="GET" action="{{ route('notes.index') }}" role="search" class="mt-4">
            <label for="search" class="sr-only">{{ __('Search notes by title or content') }}</label>

            <div class="flex items-center gap-2 sm:max-w-xs">
                <flux:input
                    id="search"
                    name="search"
                    type="search"
                    value="{{ $search }}"
                    icon="magnifying-glass"
                    placeholder="{{ __('Search notes by title or content...') }}"
                />

                @if ($search)
                    <flux:button variant="ghost" size="sm" :href="route('notes.index')">{{ __('Clear') }}</flux:button>
                @endif
            </div>
        </form>

        @if ($notes->isEmpty())
            <flux:card class="mt-3 border-dashed px-6 py-16 text-center">
                <flux:icon.document-text class="mx-auto size-12 text-gray-400 dark:text-zinc-600" />

                @if ($search)
                    <flux:heading size="sm" class="mt-4">{{ __('No notes found') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('No notes match ":search". Try a different search term.', ['search' => $search]) }}</flux:text>
                    <div class="mt-6">
                        <flux:button :href="route('notes.index')">{{ __('Clear search') }}</flux:button>
                    </div>
                @else
                    <flux:heading size="sm" class="mt-4">{{ __('No notes') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Get started by creating a new note.') }}</flux:text>
                    <div class="mt-6">
                        <flux:button variant="primary" icon="plus" :href="route('notes.create')">{{ __('New note') }}</flux:button>
                    </div>
                @endif
            </flux:card>
        @else
            <ul role="list" class="mt-3 grid grid-cols-1 gap-4 md:hidden">
                @foreach ($notes as $note)
                    <x-note-card :note="$note" />
                @endforeach
            </ul>

            <div class="mt-3 hidden md:block">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>{{ __('Title') }}</flux:table.column>
                        <flux:table.column>{{ __('Content') }}</flux:table.column>
                        <flux:table.column>{{ __('Tags') }}</flux:table.column>
                        <flux:table.column>{{ __('Created') }}</flux:table.column>
                        <flux:table.column></flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($notes as $note)
                            <flux:table.row :key="$note->id">
                                <flux:table.cell>
                                    <div class="flex items-center gap-3">
                                        <flux:avatar size="sm" name="{{ $note->title }}" color="auto" color:seed="{{ $note->id }}" />

                                        <a
                                            href="{{ route('notes.show', $note) }}"
                                            class="font-medium text-gray-900 hover:text-gray-600 dark:text-zinc-100 dark:hover:text-zinc-300"
                                        >
                                            {{ $note->title }}
                                        </a>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="max-w-xs truncate">{{ str($note->content)->stripTags()->squish()->limit(200) }}</flux:table.cell>

                                <flux:table.cell>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach ($note->tags as $tag)
                                            <x-tag-badge :tag="$tag" />
                                        @endforeach
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell class="whitespace-nowrap">
                                    <time datetime="{{ $note->created_at->toIso8601String() }}">
                                        {{ $note->created_at->diffForHumans(short: true) }}
                                    </time>
                                </flux:table.cell>

                                <flux:table.cell align="end">
                                    <div class="flex justify-end gap-1">
                                        <flux:button variant="ghost" size="sm" icon="pencil" :href="route('notes.edit', $note)">
                                            {{ __('Edit') }}<span class="sr-only">, {{ $note->title }}</span>
                                        </flux:button>

                                        <flux:modal.trigger name="delete-note-{{ $note->id }}">
                                            <flux:button variant="ghost" size="sm" icon="trash">
                                                {{ __('Delete') }}<span class="sr-only">, {{ $note->title }}</span>
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>

            @foreach ($notes as $note)
                <flux:modal name="delete-note-{{ $note->id }}" class="min-w-88">
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
            @endforeach

            @if ($notes->hasPages())
                <div class="mt-8">
                    {{ $notes->links() }}
                </div>
            @endif
        @endif
    </main>
</x-layout>
