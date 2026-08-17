<x-layout title="New tag">
    <main class="mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
        <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('tags.index')">
            All tags
        </flux:button>

        <flux:heading size="xl" class="mt-6">New tag</flux:heading>

        <x-tag-form :action="route('tags.store')" submit-label="Create tag" />
    </main>
</x-layout>
