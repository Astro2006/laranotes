<x-layout title="Notes">
    <main class="mx-auto w-full max-w-7xl px-4 py-10 sm:px-6 sm:py-14">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-sm font-medium text-gray-500 dark:text-zinc-400">
                @if ($search)
                    {{ trans_choice('{0}No notes found|{1}1 note found|[2,*]:count notes found', $notes->total(), ['count' => $notes->total()]) }}
                    for &ldquo;{{ $search }}&rdquo;
                @else
                    {{ trans_choice('{0}No notes|{1}1 note|[2,*]:count notes', $notes->total(), ['count' => $notes->total()]) }}
                @endif
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

        <form method="GET" action="{{ route('notes.index') }}" role="search" class="mt-4">
            <label for="search" class="sr-only">Search notes by title</label>

            <div class="relative sm:max-w-xs">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-4 text-gray-400 dark:text-zinc-500">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                    </svg>
                </div>

                <input
                    id="search"
                    name="search"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Search notes by title&hellip;"
                    class="block w-full rounded-md border border-gray-300 bg-white py-2 pr-9 pl-9 text-sm text-gray-900 shadow-xs transition focus:outline-2 focus:outline-offset-2 focus:outline-indigo-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                >

                @if ($search)
                    <a
                        href="{{ route('notes.index') }}"
                        aria-label="Clear search"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300"
                    >
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="size-4">
                            <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>

        @if ($notes->isEmpty())
            <div class="mt-3 rounded-md border border-dashed border-gray-300 px-6 py-16 text-center dark:border-zinc-700">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true" class="mx-auto size-12 text-gray-400 dark:text-zinc-600">
                    <path d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" stroke-width="2" vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" />
                </svg>

                @if ($search)
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-zinc-100">No notes found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">No notes match &ldquo;{{ $search }}&rdquo;. Try a different title.</p>
                    <div class="mt-6">
                        <x-button variant="secondary" :href="route('notes.index')">Clear search</x-button>
                    </div>
                @else
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
                @endif
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
