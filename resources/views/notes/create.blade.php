<x-layout title="New note">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <a
            href="{{ route('notes.index') }}"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200"
        >
            &larr; All notes
        </a>

        <h1 class="mt-6 text-2xl font-semibold tracking-tight text-gray-900 dark:text-zinc-100">
            New note
        </h1>

        <x-note-form :action="route('notes.store')" :available-tags="$availableTags" submit-label="Create note" />
    </main>
</x-layout>
