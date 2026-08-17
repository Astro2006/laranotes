<x-layout title="Edit note">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <a
            href="{{ route('notes.show', $note) }}"
            class="text-sm text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200"
        >
            &larr; Back to note
        </a>

        <h1 class="mt-6 text-2xl font-semibold tracking-tight text-gray-900 dark:text-zinc-100">
            Edit note
        </h1>

        <x-note-form
            :action="route('notes.update', $note)"
            method="PUT"
            :note="$note"
            :available-tags="$availableTags"
            submit-label="Save changes"
        />
    </main>
</x-layout>
