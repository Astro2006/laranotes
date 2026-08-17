<x-layout title="Edit note">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('notes.show', $note)">
            Back to note
        </flux:button>

        <flux:heading size="xl" class="mt-6">Edit note</flux:heading>

        <x-note-form
            :action="route('notes.update', $note)"
            method="PUT"
            :note="$note"
            :tags="$tags"
            submit-label="Save changes"
        />
    </main>
</x-layout>
