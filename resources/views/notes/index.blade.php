<x-layout title="Notes">
    <main class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 sm:py-14">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-sm font-medium text-gray-500 dark:text-zinc-400">
                {{ trans_choice('{0}No notes|{1}1 note|[2,*]:count notes', $notes->total(), ['count' => $notes->total()]) }}
            </h2>

            <x-button :href="route('notes.create')">
                <x-slot:icon>
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="-ml-0.5 size-5">
                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                    </svg>
                </x-slot:icon>
                New note
            </x-button>
        </div>

        @if ($notes->isEmpty())
            <div class="mt-3 rounded-md border border-dashed border-gray-300 px-6 py-16 text-center dark:border-zinc-700">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true" class="mx-auto size-12 text-gray-400 dark:text-zinc-600">
                    <path d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-zinc-100">No notes</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">Get started by creating a new note.</p>
                <div class="mt-6">
                    <x-button :href="route('notes.create')">
                        <x-slot:icon>
                            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="-ml-0.5 size-5">
                                <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                            </svg>
                        </x-slot:icon>
                        New note
                    </x-button>
                </div>
            </div>
        @else
            <ul role="list" class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6 lg:grid-cols-4">
                @foreach ($notes as $note)
                    <x-note-card :note="$note" />
                @endforeach
            </ul>

            @if ($notes->hasPages())
                <div class="mt-8">
                    {{ $notes->links() }}
                </div>
            @endif
        @endif
    </main>
</x-layout>
