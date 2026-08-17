<x-layout title="New note">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('notes.index')">
            All notes
        </flux:button>

        <flux:heading size="xl" class="mt-6">New note</flux:heading>

        <x-note-form :action="route('notes.store')" :tags="$tags" submit-label="Create note" />
    </main>
</x-layout>
