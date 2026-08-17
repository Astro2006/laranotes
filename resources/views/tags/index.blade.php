<x-layout title="Tags">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('notes.index')">
            All notes
        </flux:button>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
            <flux:heading size="lg">
                {{ trans_choice('{0}No tags|{1}1 tag|[2,*]:count tags', $tags->count(), ['count' => $tags->count()]) }}
            </flux:heading>

            <flux:button variant="primary" icon="plus" :href="route('tags.create')">New tag</flux:button>
        </div>

        @if ($tags->isEmpty())
            <flux:card class="mt-3 border-dashed px-6 py-16 text-center">
                <flux:icon.tag class="mx-auto size-12 text-gray-400 dark:text-zinc-600" />
                <flux:heading size="sm" class="mt-4">No tags</flux:heading>
                <flux:text class="mt-1">Get started by creating a new tag.</flux:text>
                <div class="mt-6">
                    <flux:button variant="primary" icon="plus" :href="route('tags.create')">New tag</flux:button>
                </div>
            </flux:card>
        @else
            <flux:table class="mt-3">
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Notes</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($tags as $tag)
                        <flux:table.row :key="$tag->id">
                            <flux:table.cell>
                                <flux:badge size="sm">{{ $tag->name }}</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell>{{ $tag->notes_count }}</flux:table.cell>

                            <flux:table.cell align="end">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button variant="ghost" size="sm" icon="pencil" :href="route('tags.edit', $tag)">
                                        Edit<span class="sr-only">, {{ $tag->name }}</span>
                                    </flux:button>

                                    <form method="POST" action="{{ route('tags.destroy', $tag) }}">
                                        @csrf
                                        @method('DELETE')

                                        <flux:button variant="ghost" size="sm" icon="trash" type="submit">
                                            Delete<span class="sr-only">, {{ $tag->name }}</span>
                                        </flux:button>
                                    </form>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </main>
</x-layout>
